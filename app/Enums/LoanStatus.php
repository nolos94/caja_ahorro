<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LoanStatus: string implements HasLabel, HasColor
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case DEFAULTED = 'defaulted';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::PENDING_APPROVAL => 'Pendiente de Aprobación',
            self::APPROVED => 'Aprobado',
            self::REJECTED => 'Rechazado',
            self::ACTIVE => 'Activo',
            self::COMPLETED => 'Finalizado',
            self::DEFAULTED => 'En Mora',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_APPROVAL => 'warning',
            self::APPROVED => 'info',
            self::REJECTED => 'danger',
            self::ACTIVE => 'success',
            self::COMPLETED => 'info',
            self::DEFAULTED => 'danger',
        };
    }
}