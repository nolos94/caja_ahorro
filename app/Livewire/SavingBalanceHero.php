<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SavingBalanceHero extends StatsOverviewWidget
{
   public $record;
   protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        
        return [
            Stat::make(
                'Saldo Disponible',
                '$ ' . number_format($this->record?->balance ?? 0, 2)
            )
            ->description('Balance actual de la cuenta de ahorro')
            ->icon('heroicon-m-banknotes')
            ->color('success'),
        ];
    }
}
