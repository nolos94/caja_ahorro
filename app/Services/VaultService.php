<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\VaultTransaction;


class VaultService
{
    public static function log(string $type, float $amount, string $concept, $source = null)
    {
        return DB::transaction(function () use ($type, $amount, $concept, $source) {
            // Obtenemos el saldo actual de la caja
            $lastBalance = VaultTransaction::latest('id')->value('current_vault_balance') ?? 0;

            $newBalance = ($type === 'in') 
                ? $lastBalance + $amount 
                : $lastBalance - $amount;

            if ($newBalance < 0 && $type === 'out') {
                throw new \Exception("Fondos insuficientes en la Caja Común.");
            }

            return VaultTransaction::create([
                'type' => $type,
                'amount' => $amount,
                'concept' => $concept,
                'source_id' => $source?->id,
                'source_type' => $source ? get_class($source) : null,
                'current_vault_balance' => $newBalance,
            ]);
        });
    }

    public static function getAvailableBalance(): float
    {
        return VaultTransaction::latest()->value('current_vault_balance') ?? 0;
    }
}