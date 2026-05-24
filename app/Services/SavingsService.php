<?php

namespace App\Services;

use App\Models\Saving;
use App\Services\Handlers\PagoAhorroHandler;
use App\Services\Handlers\RegularizacionHandler;

class SavingsService
{
    public function __construct(
        protected PagoAhorroHandler $pagoAhorroHandler,
        protected RegularizacionHandler $regularizacionHandler,
    ) {}

    public function process(
        Saving $saving,
        float $amount,
        string $type,
        ?string $startDate = null
    ) {
        return match ($type) {

            'pago_ahorro' => $this->pagoAhorroHandler->handle(
                $saving,
                $amount,
                $startDate
            ),

            'regularizacion' => $this->regularizacionHandler->handle(
                $saving,
                $amount,
                $startDate
            ),

            default => throw new \Exception('Tipo no soportado'),
        };
    }
}