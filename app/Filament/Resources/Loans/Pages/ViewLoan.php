<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use App\Enums\PaymentMethod;
use App\Models\Loan;
use App\Services\LoanService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;
    protected ?string $heading = 'Ver Préstamo';
    protected function getHeaderWidgets(): array
    
    {
        return [
            \App\Livewire\ClientLoansDebtHero::make([
                'record' => $this->record,
            ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // BOTÓN DE PAGO EN CASCADA
            Action::make('registrarPago')
                ->label('Registrar Cobro')
                ->icon('heroicon-m-banknotes')
                ->color('success')
                ->modalHeading('Registrar Pago Recibido')
                ->disabled(fn (Loan $record) => 
        $record->status === \App\Enums\LoanStatus::COMPLETED)
                ->modalDescription(function (Loan $record) {
        // Buscamos la cuota más antigua pendiente (UNPAID, PARTIAL o OVERDUE)
        $proximaCuota = $record->installments()
            ->where('status', '!=', \App\Enums\InstallmentStatus::PAID) // Ajusta según tu Enum
            ->orderBy('due_date', 'asc')
            ->first();

        $infoCuota = "";
        if ($proximaCuota) {
            $montoPendienteCuota = $proximaCuota->total_amount - $proximaCuota->paid_amount;
            $infoCuota = " | Próxima cuota (#{$proximaCuota->installment_number}): $" . number_format($montoPendienteCuota, 2);
        }

        return "Saldo Total: $" . number_format($record->balance, 2) . $infoCuota;
    })
    ->modalSubmitActionLabel('Confirmar Pago')
                ->form([
                    TextInput::make('total_amount')
                        ->label('Monto a Recibir')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->placeholder('Ej: 200.00'),

                    Select::make('payment_method')
                        ->label('Método de Pago')
                        ->options(PaymentMethod::class) // Usa tu Enum directamente
                        ->required()
                        ->default(PaymentMethod::CASH ?? null), // Ajusta según tu Enum
                ])
                ->action(function (array $data, LoanService $service) {
                    try {
                        // Llamamos a tu función impecable
                        $service->processPayment(
                            $this->record,           // El préstamo actual
                            (float) $data['total_amount'], 
                            auth()->id(),           // Usuario que recibe
                            $data['payment_method'] // Instancia del Enum
                        );

                        Notification::make()
                            ->title('Pago Procesado')
                            ->body('El monto se distribuyó correctamente entre las cuotas.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al procesar')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

        ];
    }
}
