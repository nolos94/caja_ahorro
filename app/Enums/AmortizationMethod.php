<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AmortizationMethod: string implements HasLabel, HasColor
{
    case FLAT = 'flat';
    case FRENCH = 'french';
    case GERMAN = 'german';
    case MANUAL = 'manual';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FLAT => 'Interés Simple (Negocio)',
            self::FRENCH => 'Sistema Francés (Cuota Fija)',
            self::GERMAN => 'Sistema Alemán (Capital Fijo)',
            self::MANUAL => 'Personalizado / Manual',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FLAT => 'info',
            self::FRENCH => 'success',
            self::GERMAN => 'warning',
            self::MANUAL => 'gray',
        };
    }
}