<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public static function createEntry(string $concept, array $items, $procedencia = null)
    {
        return DB::transaction(function () use ($concept, $items, $procedencia) {
            $date = now();

            foreach ($items as $item) {
                $account = Account::where('code', $item['account_code'])->first();

                if (!$account) {
                    throw new \Exception("La cuenta contable {$item['account_code']} no existe.");
                }

                LedgerEntry::create([
                    'account_id'       => $account->id,
                    'concept'          => $concept,
                    'debit'            => $item['debit'] ?? 0,
                    'credit'           => $item['credit'] ?? 0,
                    'entry_date'       => $date,
                    'procedencia_id'   => $procedencia?->id,
                    'procedencia_type' => $procedencia ? get_class($procedencia) : null,
                ]);

                // Llamamos a la lógica interna del service
                self::updateAccountBalance($account);
            }
        });
    }

    /**
     * Calcula el saldo según la naturaleza de la cuenta
     */
    public static function updateAccountBalance(Account $account)
    {
        $totals = LedgerEntry::where('account_id', $account->id)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        $debit = $totals->total_debit ?? 0;
        $credit = $totals->total_credit ?? 0;

        // Lógica de Partida Doble:
        // Activos y Gastos crecen por el Debe.
        // Pasivos, Capital e Ingresos crecen por el Haber.
        if (in_array($account->type, ['asset', 'expense'])) {
            $balance = $debit - $credit;
        } else {
            $balance = $credit - $debit;
        }

        $account->update(['balance' => $balance]);
    }

    /**
     * Devuelve el listado de todas las cuentas con sus saldos actuales
     */
    public static function getTrialBalance()
    {
        return Account::orderBy('code', 'asc')->get(['code', 'name', 'type', 'balance']);
    }
}