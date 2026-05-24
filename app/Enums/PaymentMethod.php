<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel, HasColor
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';
    case CARD = 'card';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => 'Efectivo',
            self::TRANSFER => 'Transferencia Bancaria',
            self::DEPOSIT => 'Depósito',
            self::CARD => 'Tarjeta de Crédito/Débito',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CASH => 'success',
            self::TRANSFER => 'info',
            self::DEPOSIT => 'warning',
            self::CARD => 'gray',
        };
    }
}