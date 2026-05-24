<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Savings\SavingResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema; 
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action; 
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;

class SavingsRelationManager extends RelationManager
{
    protected static string $relationship = 'savings';

    protected static ?string $title = 'Cuentas de Ahorro';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('balance')
                    ->label('Saldo Inicial')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->required(),
                
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        'frozen' => 'Congelada',
                    ])
                    ->default('active')
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
                
                TextColumn::make('balance')
                    ->label('Saldo Neto')
                    ->money('USD')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'frozen' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha Apertura')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                // Acción para ir al recurso principal de ahorros (SavingResource)
                Action::make('view_saving')
                    ->label('Ver Cuenta')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn ($record): string => SavingResource::getUrl('view', ['record' => $record])),
                
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}