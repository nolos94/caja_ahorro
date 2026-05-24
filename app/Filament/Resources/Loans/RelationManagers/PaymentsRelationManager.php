<?php

namespace App\Filament\Resources\Loans\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('payment_date')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
       return $table
            ->recordTitleAttribute('payment_date')
            ->columns([
                TextColumn::make('payment_date')
                    ->label('Fecha de Pago')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('amount')
    ->label('Monto Recibido')
    ->prefix('$')
    ->numeric(decimalPlaces: 2),

                TextColumn::make('payment_method')
                    ->label('Método')
                    ->badge(),

                TextColumn::make('reference_number')
                    ->label('Referencia')
                    ->placeholder('N/A'),

                TextColumn::make('receiver.name')
                    ->label('Cajero')
                    ->icon('heroicon-m-user'),
            ])
            ->filters([])
            ->headerActions([])
                    ->actions([
                    ViewAction::make()
            ->label('Ver Desglose')
            ->modalHeading('Detalle del Pago Realizado')
            ->modalWidth('3xl')
            ->schema([]) 
            ->infolist([]) 
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Cerrar')
            ->modalContent(function ($record) {
                return view('loans.payment-details-view', [
                    'payment' => $record->loadMissing('details.installment'),
                ]);
            })
            // --- AQUÍ AGREGAMOS LAS ACCIONES DEL MODAL ---
            ->modalActions([
                Action::make('print_receipt')
                    ->label('Imprimir Recibo')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->button()
                    // Descomenta esto cuando tengas la ruta de impresión creada
                    // ->url(fn ($record) => route('payments.print', $record), shouldOpenInNewTab: true)
                    ->action(function () {
                        \Filament\Notifications\Notification::make()
                            ->title('Generando Recibo')
                            ->body('La función de impresión de recibos se habilitará pronto.')
                            ->info()
                            ->send();
                    }),
    ]),
            ]);
    }
}