<?php

namespace App\Services\Amortization;

class FrenchStrategy implements AmortizationStrategy
{
    public function calculate(float $amount, float $rate, int $terms): array
    {
        $installments = [];
        $periodicRate = ($rate / 100); // Tasa decimal
        $balance = $amount;

        // Fórmula de Cuota Nivelada: [P * r * (1 + r)^n] / [(1 + r)^n - 1]
        $monthlyPayment = $amount * ($periodicRate * pow(1 + $periodicRate, $terms)) / (pow(1 + $periodicRate, $terms) - 1);
        $monthlyPayment = round($monthlyPayment, 2);

        for ($i = 1; $i <= $terms; $i++) {
            $interest = round($balance * $periodicRate, 2);
            $principal = round($monthlyPayment - $interest, 2);

            // Ajuste en la última cuota para liquidar el saldo exacto
            if ($i === $terms) {
                $principal = $balance;
                $monthlyPayment = $principal + $interest;
            }

            $installments[] = [
                'principal_amount' => $principal,
                'interest_amount' => $interest,
                'total_amount' => $monthlyPayment,
            ];

            $balance -= $principal;
        }

        return $installments;
    }
}