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

class PagoAhorroHandler
{
    public function __construct(
        protected SavingAccountingService $accounting
    ) {}

    public function handle(Saving $saving, float $amount)
    {
        return DB::transaction(function () use ($saving, $amount) {

            $transaction = SavingTransaction::create([
                'saving_id' => $saving->id,
                'type' => 'pago_ahorro',
                'amount' => $amount,
                'received_by' => auth()->id() ?? 1,
                'payment_method' => 'cash',
            ]);

            $remaining = round($amount, 2);
            $appliedTotal = 0.0;

            $installments = SavingInstallment::where('saving_id', $saving->id)
                ->where('status', '!=', 'paid')
                ->orderBy('month_year', 'asc')
                ->get();

            foreach ($installments as $inst) {

                if ($remaining <= 0) {
                    break;
                }

                $pending = round($inst->total_amount - $inst->paid_amount, 2);

                if ($pending <= 0) {
                    continue;
                }

                $applied = round(min($remaining, $pending), 2);

                SavingInstallmentPayment::create([
                    'saving_transaction_id' => $transaction->id,
                    'saving_installment_id' => $inst->id,
                    'amount' => $applied,
                ]);

                $inst->increment('paid_amount', $applied);
                $inst->refresh();

                if ($inst->paid_amount >= $inst->total_amount) {
                    $inst->update(['status' => 'paid']);
                }

                $remaining = round($remaining - $applied, 2);
                $appliedTotal = round($appliedTotal + $applied, 2);
            }

            while ($remaining > 0) {

                $last = SavingInstallment::where('saving_id', $saving->id)
                    ->orderBy('month_year', 'desc')
                    ->first();

                $next = $last
                    ? Carbon::parse($last->month_year . '-01')->addMonth()
                    : now()->startOfMonth();

                $newInstallment = SavingInstallment::create([
                    'saving_id' => $saving->id,
                    'month_year' => $next->format('Y-m'),
                    'saving_amount' => round(SavingConfig::MONTHLY_FEE * (1 - SavingConfig::FEE_PERCENTAGE), 2),
                    'fee_amount' => round(SavingConfig::MONTHLY_FEE * SavingConfig::FEE_PERCENTAGE, 2),
                    'total_amount' => SavingConfig::MONTHLY_FEE,
                    'paid_amount' => 0,
                    'status' => 'pending',
                ]);

                $applied = round(min($remaining, $newInstallment->total_amount), 2);

                SavingInstallmentPayment::create([
                    'saving_transaction_id' => $transaction->id,
                    'saving_installment_id' => $newInstallment->id,
                    'amount' => $applied,
                ]);

                $newInstallment->increment('paid_amount', $applied);
                $newInstallment->refresh();

                if ($newInstallment->paid_amount >= $newInstallment->total_amount) {
                    $newInstallment->update(['status' => 'paid']);
                }

                $remaining = round($remaining - $applied, 2);
                $appliedTotal = round($appliedTotal + $applied, 2);
            }

            $neto = round($appliedTotal * (1 - SavingConfig::FEE_PERCENTAGE), 2);
            $comision = round($appliedTotal * SavingConfig::FEE_PERCENTAGE, 2);

            $saving->increment('balance', $neto);

            $this->accounting->register(
                $saving,
                $transaction,
                $appliedTotal,
                $neto,
                $comision
            );

            return $transaction;
        });
    }
}