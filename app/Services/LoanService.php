<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Enums\LoanStatus;
use App\Enums\InstallmentStatus;
use App\Enums\LoanFrequency;
use App\Enums\AmortizationMethod;
use App\Enums\PaymentMethod;
use App\Services\Amortization\FrenchStrategy;
use App\Services\Amortization\GermanStrategy;
use App\Services\Amortization\FlatStrategy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\VaultService;
use App\Services\AccountingService;

class LoanService
{
    public function simulate(
        float $amount,
        float $rate,
        int $terms,
        LoanFrequency $frequency,
        AmortizationMethod $method,
        ?string $startDate = null,
        bool $checkFunds = true // Agregamos un flag para controlar la validación
    ): Collection {
        
        // --- VALIDACIÓN DE FONDOS ---
        if ($checkFunds) {
            $disponible = $this->getAvailableFundsForLoans();
            if ($amount > $disponible) {
                throw new \Exception("Fondos insuficientes. Disponible para préstamos: $" . number_format($disponible, 2));
            }
        }

        $strategy = match ($method) {
            AmortizationMethod::FRENCH => new FrenchStrategy(),
            AmortizationMethod::GERMAN => new GermanStrategy(),
            AmortizationMethod::FLAT   => new FlatStrategy(),
            AmortizationMethod::MANUAL => new FlatStrategy(),
        };

        $data = $strategy->calculate($amount, $rate, $terms);
        $installments = collect();
        $currentDate = Carbon::parse($startDate ?? now());

        foreach ($data as $index => $item) {
            $currentDate = $frequency->applyToDate($currentDate->copy());

            $installments->push((object) [
                'installment_number' => $index + 1,
                'due_date'           => $currentDate->format('Y-m-d'),
                'principal_amount'   => $item['principal_amount'],
                'interest_amount'    => $item['interest_amount'],
                'total_amount'       => $item['total_amount'],
                'status'             => InstallmentStatus::UNPAID,
            ]);
        }

        return $installments;
    }

    public function persistInstallments(Loan $loan): void
    {
        // Volvemos a validar antes de persistir por seguridad
        $disponible = $this->getAvailableFundsForLoans();
        if ($loan->amount > $disponible) {
            throw new \Exception("No hay fondos suficientes para desembolsar. Disponible: $" . number_format($disponible, 2));
        }

        DB::transaction(function () use ($loan) {
            $loan->installments()->delete();

            // Llamamos a simulate pasando checkFunds: false porque ya validamos arriba
            $simulation = $this->simulate(
                (float) $loan->amount,
                (float) $loan->interest_rate,
                (int) $loan->installments_count,
                $loan->frequency,
                $loan->amortization_method,
                $loan->disbursement_date ? $loan->disbursement_date->format('Y-m-d') : null,
                false 
            );

            foreach ($simulation as $data) {
                $loan->installments()->create([
                    'installment_number' => $data->installment_number,
                    'due_date' => $data->due_date,
                    'principal_amount' => $data->principal_amount,
                    'interest_amount' => $data->interest_amount,
                    'total_amount' => $data->total_amount,
                    'paid_amount' => 0,
                    'status' => InstallmentStatus::UNPAID,
                ]);
            }

            // --- CONTABILIDAD DE DESEMBOLSO ---
            $amount = (float) $loan->amount;
            VaultService::log('out', $amount, "Desembolso Préstamo #{$loan->id}", $loan);

            AccountingService::createEntry(
                "Desembolso de préstamo #{$loan->id}",
                [ // Segundo argumento: el array de cuentas
                    ['account_code' => '102.01', 'debit' => $amount, 'credit' => 0],
                    ['account_code' => '101.01', 'debit' => 0, 'credit' => $amount],
                ],
                $loan // Tercer argumento: la procedencia
            );
        });
    }

    public function processPayment(Loan $loan, float $totalAmount, $receivedBy, PaymentMethod $method): void 
    {
        DB::transaction(function () use ($loan, $totalAmount, $receivedBy, $method) {
            $remaining = $totalAmount;
            $paymentHeader = $loan->payments()->create([
                'amount' => $totalAmount,
                'payment_date' => now(),
                'received_by' => $receivedBy,
                'payment_method' => $method,
            ]);

            $installments = $loan->installments()
                ->whereIn('status', [InstallmentStatus::UNPAID, InstallmentStatus::PARTIAL, InstallmentStatus::OVERDUE])
                ->orderBy('due_date')->get();

            $totalPrincipalPaid = 0;
            $totalInterestPaid = 0;

            foreach ($installments as $installment) {
                if ($remaining <= 0) break;

                $pendingAmount = $installment->total_amount - $installment->paid_amount;
                $appliedToThisInstallment = min($remaining, $pendingAmount);

                $alreadyPaidInterest = min($installment->paid_amount, $installment->interest_amount);
                $remainingInterestInInstallment = $installment->interest_amount - $alreadyPaidInterest;

                $interestPaidToday = min($appliedToThisInstallment, $remainingInterestInInstallment);
                $principalPaidToday = $appliedToThisInstallment - $interestPaidToday;

                $totalInterestPaid += $interestPaidToday;
                $totalPrincipalPaid += $principalPaidToday;

                $installment->paid_amount += $appliedToThisInstallment;
                $installment->status = ($installment->paid_amount >= $installment->total_amount) 
                    ? InstallmentStatus::PAID : InstallmentStatus::PARTIAL;
                $installment->save();

                $paymentHeader->details()->create([
                    'loan_installment_id' => $installment->id,
                    'amount' => $appliedToThisInstallment,
                ]);

                $remaining -= $appliedToThisInstallment;
            }

            $newBalance = $loan->installments()->sum('total_amount') - $loan->installments()->sum('paid_amount');
            $loan->update([
                'balance' => $newBalance,
                'status' => ($newBalance <= 0) ? LoanStatus::COMPLETED : $loan->status
            ]);

            // --- CONTABILIDAD ---
            VaultService::log('in', $totalAmount, "Recaudo Préstamo #{$loan->id} (K:{$totalPrincipalPaid} I:{$totalInterestPaid})", $paymentHeader);
            AccountingService::createEntry(
                "Recaudo cuota #{$loan->id}", 
                [ // Segundo argumento: items
                    ['account_code' => '101.01', 'debit' => $totalAmount, 'credit' => 0],
                    ['account_code' => '102.01', 'debit' => 0, 'credit' => $totalPrincipalPaid],
                    ['account_code' => '401.01', 'debit' => 0, 'credit' => $totalInterestPaid],
                ],
                $paymentHeader // Tercer argumento: procedencia
            );
        });
    }

    public function calculateTotals(float $amount, float $rate, int $terms, LoanFrequency $frequency, AmortizationMethod $method): array 
    {
        // En calculateTotals pasamos checkFunds: false para que el simulador de la UI no de error mientras escriben
        $simulation = $this->simulate($amount, $rate, $terms, $frequency, $method, now()->format('Y-m-d'), false);

        $totalAmount = $simulation->sum('total_amount');
        $totalInterest = $simulation->sum('interest_amount');

        return [
            'total_interest' => round($totalInterest, 2),
            'total_amount'   => round($totalAmount, 2),
            'balance'        => round($totalAmount, 2),
        ];
    }

    public function getAvailableFundsForLoans(): float
    {
        $cajaGeneral = \App\Models\Account::where('code', '101.01')->first()->balance ?? 0;
        $fondoGastos = \App\Models\Account::where('code', '401.02')->first()->balance ?? 0;
        return max(0, $cajaGeneral - $fondoGastos);
    }
}