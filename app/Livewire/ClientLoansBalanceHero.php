<?php


namespace App\Livewire;
use App\Enums\LoanStatus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientLoansBalanceHero extends StatsOverviewWidget
{
   public $record;

    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalDebt = $this->record
            ? $this->record->loans()
                ->where('status', LoanStatus::ACTIVE)
                ->sum('balance')
            : 0;

        return [
            Stat::make(
                'Deuda Activa',
                '$ ' . number_format($totalDebt, 2)
            )
            ->description('Solo préstamos activos')
            ->icon('heroicon-m-credit-card')
            ->color('danger'),
        ];
    }
}
