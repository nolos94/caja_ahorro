<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InstallmentStatus: string implements HasLabel, HasColor
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNPAID => 'Pendiente',
            self::PARTIAL => 'Pago Parcial',
            self::PAID => 'Pagado',
            self::OVERDUE => 'Vencido',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNPAID => 'gray',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
            self::OVERDUE => 'danger',
        };
    }
}