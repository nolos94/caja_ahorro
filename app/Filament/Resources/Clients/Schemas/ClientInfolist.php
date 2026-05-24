<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECCIÓN 1: Datos de Usuario/Acceso
                Section::make('Datos de Acceso (Usuario)')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Alias / Username')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('user.email')
                            ->label('Correo Electrónico')
                            ->icon('heroicon-o-envelope'),
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                    ]),

                // SECCIÓN 2: Información Personal y Financiera
                Section::make('Información Personal y Financiera')
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Nombre Completo / Razón Social')
                            ->weight('bold'),
                        TextEntry::make('dni_ruc')
                            ->label('DNI / RUC'),
                        TextEntry::make('phone')
                            ->label('Teléfono'),
                        TextEntry::make('credit_limit')
                            ->label('Límite de Crédito')
                            ->money('USD'),
                        TextEntry::make('address')
                            ->label('Dirección')
                            ->columnSpanFull()
                            ->placeholder('No registrada'),
                    ]),

                // SECCIÓN 3: Datos Bancarios
                Section::make('Datos Bancarios (Para Desembolsos)')
                    ->collapsible()
                    ->collapsed() // Empieza cerrada para limpieza visual
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bank_name')
                            ->label('Nombre del Banco')
                            ->placeholder('No especificado'),
                        TextEntry::make('account_number')
                            ->label('Número de Cuenta')
                            ->placeholder('No especificado'),
                    ]),

                // SECCIÓN 4: Auditoría básica
                Section::make('Registro')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Registro')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime(),
                    ]),
            ]);
    }
}