<?php

namespace App\Services\Amortization;

class GermanStrategy implements AmortizationStrategy
{
    public function calculate(float $amount, float $rate, int $terms): array
    {
        $installments = [];
        $periodicRate = ($rate / 100);
        $principalPerInstallment = round($amount / $terms, 2);
        $balance = $amount;

        for ($i = 1; $i <= $terms; $i++) {
            $interest = round($balance * $periodicRate, 2);
            
            if ($i === $terms) {
                $principalPerInstallment = $balance;
            }

            $totalPerInstallment = $principalPerInstallment + $interest;

            $installments[] = [
                'principal_amount' => $principalPerInstallment,
                'interest_amount' => $interest,
                'total_amount' => $totalPerInstallment,
            ];

            $balance -= $principalPerInstallment;
        }

        return $installments;
    }
}