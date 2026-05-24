<?php

namespace App\Services\Handlers;

use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Models\SavingInstallment;
use App\Models\SavingInstallmentPayment;
use App\Support\SavingConfig;
use App\Services\Handlers\SavingAccountingService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegularizacionHandler
{
    public function __construct(
        protected SavingAccountingService $accounting
    ) {}

    public function handle(Saving $saving, float $amount, ?string $startDate)
    {
        return DB::transaction(function () use ($saving, $amount, $startDate) {

            $transaction = SavingTransaction::create([
                'saving_id' => $saving->id,
                'type' => 'regularizacion',
                'amount' => $amount,
                'received_by' => auth()->id() ?? 1,
                'payment_method' => 'cash',
            ]);

            $this->generateFullInstallmentRange($saving, $startDate);

            $remaining = $amount;
            $appliedTotal = 0.0;

            $installments = SavingInstallment::where('saving_id', $saving->id)
                ->orderBy('month_year')
                ->get();

            foreach ($installments as $installment) {
                if ($remaining <= 0) break;
                $remaining = $this->applyPayment($transaction, $installment, $remaining, $appliedTotal);
            }

            if ($remaining > 0) {
                $remaining = $this->createFutureInstallments($saving, $transaction, $remaining, $appliedTotal);
            }

            $totalApplied = $amount - $remaining;

            $neto = $totalApplied * (1 - SavingConfig::FEE_PERCENTAGE);
            $comision = $totalApplied * SavingConfig::FEE_PERCENTAGE;

            $saving->increment('balance', $neto);

            $this->accounting->register(
                $saving,
                $transaction,
                $totalApplied,
                $neto,
                $comision
            );

            return $transaction;
        });
    }

    private function generateFullInstallmentRange(Saving $saving, ?string $startDate): void
    {
        $firstMonth = SavingInstallment::where('saving_id', $saving->id)->min('month_year');

        $current = $startDate
            ? Carbon::parse($startDate)->startOfMonth()
            : ($firstMonth
                ? Carbon::parse($firstMonth . '-01')->startOfMonth()
                : now()->startOfMonth());

        $target = now()->startOfMonth();

        while ($current <= $target) {
            SavingInstallment::firstOrCreate(
                [
                    'saving_id' => $saving->id,
                    'month_year' => $current->format('Y-m'),
                ],
                [
                    'saving_amount' => SavingConfig::MONTHLY_FEE * (1 - SavingConfig::FEE_PERCENTAGE),
                    'fee_amount' => SavingConfig::MONTHLY_FEE * SavingConfig::FEE_PERCENTAGE,
                    'total_amount' => SavingConfig::MONTHLY_FEE,
                    'paid_amount' => 0,
                    'status' => 'pending',
                ]
            );

            $current->addMonth();
        }
    }

    private function applyPayment(SavingTransaction $transaction, SavingInstallment $installment, float $amount, float &$appliedTotal): float
    {
        $pending = $installment->total_amount - $installment->paid_amount;

        if ($pending <= 0) return $amount;

        $applied = min($amount, $pending);

        SavingInstallmentPayment::create([
            'saving_transaction_id' => $transaction->id,
            'saving_installment_id' => $installment->id,
            'amount' => $applied,
        ]);

        $installment->increment('paid_amount', $applied);

        $appliedTotal += $applied;

        if ($installment->paid_amount >= $installment->total_amount) {
            $installment->update(['status' => 'paid']);
        }

        return $amount - $applied;
    }

    private function createFutureInstallments(Saving $saving, SavingTransaction $transaction, float $remaining, float &$appliedTotal): float
    {
        $last = SavingInstallment::where('saving_id', $saving->id)
            ->orderBy('month_year', 'desc')
            ->first();

        $next = $last
            ? Carbon::parse($last->month_year . '-01')->addMonth()
            : now()->startOfMonth();

        while ($remaining > 0) {

            $installment = SavingInstallment::create([
                'saving_id' => $saving->id,
                'month_year' => $next->format('Y-m'),
                'saving_amount' => SavingConfig::MONTHLY_FEE * (1 - SavingConfig::FEE_PERCENTAGE),
                'fee_amount' => SavingConfig::MONTHLY_FEE * SavingConfig::FEE_PERCENTAGE,
                'total_amount' => SavingConfig::MONTHLY_FEE,
                'paid_amount' => 0,
                'status' => 'pending',
            ]);

            $applied = min($remaining, $installment->total_amount);

            SavingInstallmentPayment::create([
                'saving_transaction_id' => $transaction->id,
                'saving_installment_id' => $installment->id,
                'amount' => $applied,
            ]);

            $installment->increment('paid_amount', $applied);

            $appliedTotal += $applied;

            if ($installment->paid_amount >= $installment->total_amount) {
                $installment->update(['status' => 'paid']);
            }

            $remaining -= $applied;

            $next->addMonth();
        }

        return $remaining;
    }
}