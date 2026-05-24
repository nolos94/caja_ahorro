<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de Acceso (Usuario)')
                    ->columnSpanFull() // Esto asegura el ancho completo
                    ->schema([
                        TextInput::make('username')
                            ->label('Alias / Username')
                            ->required()
                            ->unique('users', 'name', ignoreRecord: true),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->visibleOn('create'),
                    ])->columns(2),

                Section::make('Información Personal y Financiera')
                    ->columnSpanFull() // Esto asegura el ancho completo
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nombre Completo / Razón Social')
                            ->required(),
                        TextInput::make('dni_ruc')
                            ->label('DNI / RUC')
                            ->required(),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                        TextInput::make('credit_limit')
                            ->label('Límite de Crédito')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                                'blocked' => 'Bloqueado',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Section::make('Datos Bancarios (Para Desembolsos)')
                    ->columnSpanFull() // Esto asegura el ancho completo
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Nombre del Banco'),
                        TextInput::make('account_number')
                            ->label('Número de Cuenta'),
                    ])->columns(2),
            ]);
    }
}