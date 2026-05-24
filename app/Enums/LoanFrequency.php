<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Carbon;

enum LoanFrequency: string implements HasLabel
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case BIWEEKLY = 'biweekly';
    case MONTHLY = 'monthly';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DAILY => 'Diario',
            self::WEEKLY => 'Semanal',
            self::BIWEEKLY => 'Quincenal',
            self::MONTHLY => 'Mensual',
        };
    }

    /**
     * Aplica la frecuencia a una fecha dada.
     * Centralizamos aquí si es addDay, addWeek o addMonth.
     */
    public function applyToDate(Carbon $date): Carbon
    {
        return match ($this) {
            self::DAILY => $date->addDay(),
            self::WEEKLY => $date->addWeek(),
            self::BIWEEKLY => $date->addDays(15),
            self::MONTHLY => $date->addMonth(), 
        };
    }
}