<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECCIÓN 1: Ocupa todo el ancho disponible
                Section::make('Información del Préstamo')
                    ->description('Detalles generales del crédito')
                    ->collapsible()
                    ->columnSpanFull() // <-- Esto hace que la sección ocupe todo el ancho
                    ->columns([
                        'sm' => 2,
                        'lg' => 3,
                        'xl' => 4, // En pantallas grandes se reparte en 4 columnas
                    ])
                    ->schema([
                        TextEntry::make('client.full_name')->label('Client'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('amortization_method')->badge(),
                        TextEntry::make('amount')->numeric(),
                        TextEntry::make('interest_rate')->numeric()->suffix('%'),
                        TextEntry::make('installments_count')->numeric(),
                        TextEntry::make('frequency')->badge(),
                        TextEntry::make('total_interest')->numeric(),
                        TextEntry::make('total_amount')->numeric(),
                        TextEntry::make('balance')
                            ->numeric()
                            ->weight('bold')
                            ->color('danger'),
                    ]),

                // SECCIÓN 2: Auditoría
                Section::make('Fechas y Auditoría')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull() // <-- También a lo ancho
                    ->columns([
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->schema([
                        TextEntry::make('approved_at')->dateTime()->placeholder('-'),
                        TextEntry::make('approved_by')->numeric()->placeholder('-'),
                        TextEntry::make('disbursement_date')->date()->placeholder('-'),
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ]),

                Section::make('Notas')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('notes')->label(false)->placeholder('-'),
                    ]),
            ]);
    }
}