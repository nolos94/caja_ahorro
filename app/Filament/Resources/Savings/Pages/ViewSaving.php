<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use App\Models\Saving;
use App\Services\SavingsService;
use App\Enums\PaymentMethod; // Asegúrate de tener este Enum o cámbialo por un array
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\DatePicker;

class ViewSaving extends ViewRecord
{
    protected static string $resource = SavingResource::class;
    protected function getHeaderWidgets(): array
{
    return [
        \App\Livewire\SavingBalanceHero::make([
            'record' => $this->record,
        ]),
    ];
}

    public function getTitle(): string
{
    $client = $this->record->client;

    if (! $client) {
        return 'Sin cliente';
    }

    $position = $client->savings()
        ->orderBy('id')
        ->pluck('id')
        ->search($this->record->id);

    return $client->full_name . ' ' . ($position + 1);
}
    

    protected function getHeaderActions(): array
    {
        return [
            // BOTÓN DE DEPÓSITO (PAGO DE CUOTAS)
            Action::make('registrarDeposito')
                ->label('Registrar Depósito')
                ->icon('heroicon-m-banknotes')
                ->color('success')
                ->modalHeading('Registrar Depósito de Ahorro')
                ->modalDescription(function (Saving $record) {
                    // Buscamos la última cuota pendiente para informar al usuario
                    $proximaCuota = $record->installments()
                        ->where('status', '!=', 'paid')
                        ->orderBy('month_year', 'asc')
                        ->first();

                    $infoCuota = "";
                    if ($proximaCuota) {
                        $montoPendienteCuota = $proximaCuota->total_amount - $proximaCuota->paid_amount;
                        $infoCuota = " | Próxima cuota ({$proximaCuota->month_year}): $" . number_format($montoPendienteCuota, 2);
                    }

                    return "Saldo actual en cuenta: $" . number_format($record->balance, 2) . $infoCuota;
                })
                ->modalSubmitActionLabel('Confirmar Depósito')
                ->form([
                    TextInput::make('amount')
                        ->label('Monto a Depositar')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->placeholder('10.00')
                        ->default(25.00), // Monto de la cuota mensual por defecto

                    Select::make('payment_method')
                        ->label('Método de Pago')
                        // Si no usas Enum de préstamos, puedes usar un array: ['cash' => 'Efectivo', 'transfer' => 'Transferencia']
                        ->options([
                            'cash' => 'Efectivo',
                            'transfer' => 'Transferencia Bancaria',
                        ])
                        ->required()
                        ->default('cash'),
                    DatePicker::make('start_date')
                        ->label('Fecha de inicio')
                        ->nullable()
                        ->default(null),
                    Select::make('type')
                        ->label('Tipo de operación')
                        ->options([
                            'pago_ahorro' => 'Pago de Ahorro',
                            'regularizacion' => 'Regularización (Migración)',
                        ])
                        ->required()
                        ->default('pago_ahorro')
                        ->reactive(),
                ])
                ->action(function (array $data, SavingsService $service) {
                    try {

                        $service->process(
                            saving: $this->record,
                            amount: (float) $data['amount'],
                            type: $data['type'],
                            startDate: $data['start_date'] ?? null
                        );

                        Notification::make()
                            ->title('Operación procesada')
                            ->body('El movimiento fue registrado correctamente.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['balance']);

                    } catch (\Exception $e) {

                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}