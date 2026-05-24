<?php

namespace App\Services\Amortization;
/*
class FlatStrategy implements AmortizationStrategy
{
    public function calculate(float $amount, float $rate, int $terms): array
    {
        $installments = [];
        $totalInterest = round($amount * ($rate / 100), 2);
        $totalLoan = $amount + $totalInterest;

        $principalPerInstallment = round($amount / $terms, 2);
        $interestPerInstallment = round($totalInterest / $terms, 2);
        $totalPerInstallment = round($totalLoan / $terms, 2);

        for ($i = 1; $i <= $terms; $i++) {
            // Ajuste de centavos en la última cuota
            if ($i === $terms) {
                $principalPerInstallment = $amount - (round($amount / $terms, 2) * ($terms - 1));
                $interestPerInstallment = $totalInterest - (round($totalInterest / $terms, 2) * ($terms - 1));
                $totalPerInstallment = $principalPerInstallment + $interestPerInstallment;
            }

            $installments[] = [
                'principal_amount' => $principalPerInstallment,
                'interest_amount' => $interestPerInstallment,
                'total_amount' => $totalPerInstallment,
            ];
        }

        return $installments;
    }
}
    */
class FlatStrategy implements AmortizationStrategy
{
    public function calculate(float $amount, float $rate, int $terms): array
    {
        $installments = [];

        // interés TOTAL del préstamo (NO se divide)
        $totalInterest = round($amount * ($rate / 100), 2);

        // capital por cuota
        $principalPerInstallment = round($amount / $terms, 2);

        // interés FIJO por cuota (se repite igual siempre)
        $interestPerInstallment = $totalInterest;

        $totalPerInstallment = round(
            $principalPerInstallment + $interestPerInstallment,
            2
        );

        for ($i = 1; $i <= $terms; $i++) {
            $installments[] = [
                'principal_amount' => $principalPerInstallment,
                'interest_amount' => $interestPerInstallment,
                'total_amount' => $totalPerInstallment,
            ];
        }

        return $installments;
    }
}