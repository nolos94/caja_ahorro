<?php

namespace App\Filament\Resources\Loans\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('installment_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

   public function table(Table $table): Table
{
    return $table
        ->recordTitleAttribute('installment_number')
        ->columns([
            TextColumn::make('installment_number')
                ->label('N°')
                ->sortable(),
            TextColumn::make('due_date')
                ->label('Fecha de Vencimiento')
                ->date('d/m/Y')
                ->sortable(),
            TextColumn::make('total_amount')
                ->label('Cuota')
                ->money('USD'),
            TextColumn::make('paid_amount')
                ->label('Pagado')
                ->money('USD'),
            TextColumn::make('status')
                ->label('Estado')
                ->badge(),
        ])
        ->filters([])
        ->headerActions([])
        ->actions([
            // Aquí podrías poner un botón de "Registrar Pago" a futuro
            EditAction::make()->label('Editar'),
        ]);
}
}
