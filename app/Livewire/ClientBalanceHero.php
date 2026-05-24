<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientBalanceHero extends StatsOverviewWidget
{
    public $record;

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        $total = $this->record
            ? $this->record->savings()->sum('balance')
            : 0;

        return [
            Stat::make(
                'Saldo Total del Cliente',
                '$ ' . number_format($total, 2)
            )
            ->description('Suma de todas las cuentas de ahorro')
            ->icon('heroicon-m-banknotes')
            ->color('success'),
        ];
    }
}
