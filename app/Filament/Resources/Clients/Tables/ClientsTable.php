<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
->columns([
                // 1. Nombre completo del cliente
                TextColumn::make('full_name')
                    ->label('Nombre del Cliente')
                    ->searchable()
                    ->sortable(),

                // 2. Identificación
                TextColumn::make('dni_ruc')
                    ->label('DNI / RUC')
                    ->searchable(),

                // 3. Teléfono
                TextColumn::make('phone')
                    ->label('Teléfono'),

                // 4. Límite de crédito con formato moneda
                TextColumn::make('credit_limit')
                    ->label('Límite de Crédito')
                    ->money('USD') // Cámbialo por tu moneda local si es necesario
                    ->sortable(),

                // 5. Estado con formato de insignia (badge)
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),

                // 6. Fecha de creación
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
