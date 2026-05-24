<?php

namespace App\Services\Amortization;

interface AmortizationStrategy
{
    /**
     * Calcula el cuadro de amortización.
     * 
     * @param float $amount Monto capital solicitado.
     * @param float $rate Tasa de interés (porcentaje, ej: 5 para 5%).
     * @param int $terms Número de cuotas.
     * @return array Lista de cuotas con principal_amount e interest_amount.
     */
    public function calculate(float $amount, float $rate, int $terms): array;
}