<?php

namespace App\Filament\Resources\Savings\Schemas;

use Filament\Infolists\Components\TextEntry;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SavingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECCIÓN 1: Información General de la Cuenta
                Section::make('Información de la Cuenta de Ahorro')
                    ->description('Estado actual y balance del cliente')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 2,
                        'lg' => 3,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('client.full_name')
                            ->label('Cliente'),
                        
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                'blocked' => 'warning',
                                default => 'gray',
                            }),
                    ]),

            ]);
    }
}