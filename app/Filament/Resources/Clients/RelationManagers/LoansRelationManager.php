<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema; // <--- Este es el que me dices que funciona
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action; // <--- Action de tabla para el botón
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;


class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    protected static ?string $title = 'Préstamos del Cliente';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD'),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('USD'),
                TextColumn::make('balance')
                    ->label('Saldo Pendiente')
                    ->money('USD')
                    ->color('danger'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Dejamos vacío o solo CreateAction si lo necesitas
            ])
            ->actions([
                // ESTA ES LA ACCIÓN CLAVE QUE ME PEDISTE
                Action::make('view_loan')
                    ->label('Ver Préstamo')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn ($record): string => LoanResource::getUrl('view', ['record' => $record])),

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}