<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientLoansDebtHero extends StatsOverviewWidget
{
    public $record;
    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Deuda Pendiente',
                '$ ' . number_format($this->record?->balance ?? 0, 2)
            )
            ->description('Saldo restante del préstamo')
            ->icon('heroicon-m-credit-card')
            ->color('danger'),
        ];
    }
}
