<?php

namespace App\Filament\Resources\Loans\Schemas;

use App\Enums\AmortizationMethod;
use App\Enums\LoanFrequency;
use App\Enums\LoanStatus;
use App\Services\LoanService;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Actions; // IMPORTANTE: Esta es la clave
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- ENTRADA DE DATOS ---
                Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'full_name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),

                Select::make('amortization_method')
                    ->label('Método')
                    ->options(AmortizationMethod::class)
                    ->default(AmortizationMethod::FLAT->value)
                    ->required()
                    ->live(),

                TextInput::make('amount')
                    ->label('Valor a solicitar')
                    ->required()
                    ->numeric()
                    ->live()
                    ->prefix('$')
                    ->maxValue(function () {
                        return app(LoanService::class)
                            ->getAvailableFundsForLoans();
                    })
                    ->validationMessages([
                        'max' => 'Fondos insuficientes para otorgar este préstamo.',
                    ]),

                TextInput::make('interest_rate')
                    ->label('Interes %')
                    ->required()
                    ->numeric()
                    ->live()
                    ->suffix('%'),

                Select::make('installments_count')
                ->label('Número de Cuotas')
                ->required()
                ->options(
                    collect(range(1, 18))
                        ->mapWithKeys(fn ($i) => [$i => $i])
                        ->toArray()
                )
                ->live(),

                Select::make('frequency')
                    ->label('Modalidad')
                    ->options(LoanFrequency::class)
                    ->required()
                    ->live(),
                Actions::make([
                   Action::make('calculate')
    ->label('Generar Cotización')
    ->icon('heroicon-m-calculator')
    ->color('success')
    ->button()
    ->action(function (Set $set, Get $get) {

        $available = app(LoanService::class)
            ->getAvailableFundsForLoans();

        $amount = (float) $get('amount');

        // =========================================
        // VALIDAR FONDOS
        // =========================================
        if ($amount > $available) {

            \Filament\Notifications\Notification::make()
                ->title('Fondos insuficientes')
                ->body(
                    'Disponible para préstamos: $' .
                    number_format($available, 2)
                )
                ->danger()
                ->send();

            return;
        }

        // =========================================
        // VALIDAR CAMPOS
        // =========================================
        if (
            !$get('amount') ||
            !$get('interest_rate') ||
            !$get('installments_count') ||
            !$get('frequency')
        ) {
            return;
        }

        $freqValue = $get('frequency');

        $frequency = $freqValue instanceof LoanFrequency
            ? $freqValue
            : LoanFrequency::from($freqValue);

        $methodValue = $get('amortization_method');

        $method = $methodValue instanceof AmortizationMethod
            ? $methodValue
            : AmortizationMethod::from($methodValue);

        $service = new LoanService();

        $totals = $service->calculateTotals(
            amount: $amount,
            rate: (float) $get('interest_rate'),
            terms: (int) $get('installments_count'),
            frequency: $frequency,
            method: $method
        );

        $set('total_interest', $totals['total_interest']);
        $set('total_amount', $totals['total_amount']);
        $set('balance', $totals['balance']);
        $set('is_calculated', true);
    }),
                ])->columnSpanFull(),

                // --- CAMPOS DE TOTALES ---
                Grid::make(3)
                    ->schema([
                        TextInput::make('total_interest')
                            ->readOnly()
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('total_amount')
                            ->readOnly()
                            ->numeric()
                            ->prefix('$'),

                        TextInput::make('balance')
                            ->readOnly()
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('is_calculated')),

                // --- BOTÓN PARA VER DETALLE (También como Actions) ---
                // --- BOTÓN PARA VER DETALLE (Corregido: Enuelto en Actions::make) ---
Actions::make([
    Action::make('preview')
        ->label('Simular Cuotas')
        ->color('info')
        ->icon('heroicon-o-calculator')
        ->button()
        ->visible(fn (Get $get) => $get('is_calculated')) // <--- Oculto hasta que se calcule
        ->modalContent(function ($livewire, \App\Services\LoanService $service) {
            $data = $livewire->form->getRawState();

            return view('loans.installments-preview', [
                'installments' => $service->simulate(
                    amount: (float) ($data['amount'] ?? 0),
                    rate: (float) ($data['interest_rate'] ?? 0),
                    terms: (int) ($data['installments_count'] ?? 0),
                    frequency: \App\Enums\LoanFrequency::tryFrom($data['frequency'] ?? ''),
                    method: \App\Enums\AmortizationMethod::tryFrom($data['amortization_method'] ?? ''),
                    startDate: $data['start_date'] ?? now()->format('Y-m-d')
                ),
            ]);
        })
        ->modalActions([
        Action::make('print_pdf')
    ->label('Imprimir Cotización')
    ->color('success')
    ->icon('heroicon-o-printer')
    ->button()
    // Comentamos el URL para que no busque la ruta que no existe todavía
    // ->url(fn (Get $get) => route('loans.print-pdf', [...])) 
    // ->openUrlInNewTab()
    ->action(function () {
        // Por ahora, solo lanzamos una notificación para que veas que funciona
        \Filament\Notifications\Notification::make()
            ->title('Próximamente')
            ->body('La generación de PDF estará disponible en la siguiente fase.')
            ->info()
            ->send();
    }),
    ])
        ->modalWidth(Width::ThreeExtraLarge) 
        ->modalHeading('Cronograma de Pagos Sugerido')
        ->modalSubmitAction(false)
        ->modalCancelActionLabel('Cerrar'),
])->columnSpanFull(),


TextInput::make('status')
    ->default(LoanStatus::DRAFT->value)
    ->hidden()
    ->dehydrated(),

Placeholder::make('status_preview')
    ->label('Estado')
    ->default (LoanStatus::DRAFT->getLabel())
    ->color(LoanStatus::DRAFT->getColor()),
 
                TextInput::make('is_calculated')
                    ->default(false)
                    ->hidden()
                    ->dehydrated(false),

                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}