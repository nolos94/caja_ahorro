<?php

namespace App\Filament\Resources\Savings\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema; // Nota: En versiones recientes se usa Filament\Forms\Form
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\Action; // Importante para modalActions
use Filament\Forms\Components\TextInput;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Movimientos de la Cuenta';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')->numeric()->prefix('$')->required(),
            TextInput::make('reference'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deposit' => 'Depósito',
                        'withdrawal' => 'Retiro',
                        default => $state,
                    })
                    ->color(fn (string $state): string => $state === 'deposit' ? 'success' : 'danger'),
                TextColumn::make('amount')
                    ->label('Monto Total')
                    ->money('USD'),
                TextColumn::make('payment_method')
                    ->label('Método'),
                TextColumn::make('reference')
                    ->label('Referencia')
                    ->limit(30),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver Desglose')
                    ->modalHeading('Distribución del Pago (Cuotas)')
                    ->modalWidth('3xl')
                    ->schema([]) 
                    ->infolist([]) 
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(function ($record) {
                        $record->load(['installmentPayments.installment']); 
                        
                        return view('savings.transaction-details-view', [
                            'transaction' => $record,
                        ]);
                    })
                    ->modalActions([
                        Action::make('print_receipt')
                            ->label('Imprimir Recibo')
                            ->color('success')
                            ->icon('heroicon-o-printer')
                            ->button()
                            ->action(function () {
                                \Filament\Notifications\Notification::make()
                                    ->title('Generando Recibo')
                                    ->body('La función de impresión de recibos de ahorros se habilitará pronto.')
                                    ->info()
                                    ->send();
                            }),
                    ]),
            ])
            ->bulkActions([])
            ->headerActions([])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }
}