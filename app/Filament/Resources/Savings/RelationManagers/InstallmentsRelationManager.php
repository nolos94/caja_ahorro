<?php

namespace App\Filament\Resources\Savings\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    protected static ?string $title = 'Cuotas de Ahorro Programado';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('month_year')
                    ->label('Periodo')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('month_year')
            ->columns([
                TextColumn::make('month_year')
                    ->label('Mes/Año')
                    ->sortable(),

                TextColumn::make('saving_amount')
                    ->label('Ahorro')
                    ->money('USD'),

                TextColumn::make('fee_amount')
                    ->label('Comisión')
                    ->money('USD'),

                TextColumn::make('total_amount')
                    ->label('Total Cuota')
                    ->money('USD')
                    ->weight('bold'),

                TextColumn::make('paid_amount')
                    ->label('Pagado')
                    ->money('USD')
                    ->color(fn ($record) => $record->paid_amount >= $record->total_amount ? 'success' : 'warning'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'danger',
                        'partial' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([]) // Sin acciones, solo informativo
            ->bulkActions([]);
    }
}