<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\LoanService;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
    protected ?string $heading = 'Crear Préstamo';
    public function getSubheading(): ?string
    {
        $available = app(LoanService::class)
            ->getAvailableFundsForLoans();

        return 'Fondos disponibles para préstamos: $' . number_format($available, 2);
    }
}
