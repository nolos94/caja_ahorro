<?php

namespace App\Services\Handlers;

use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Services\VaultService;
use App\Services\AccountingService;

class SavingAccountingService
{
    public function register(
        Saving $saving,
        SavingTransaction $transaction,
        float $bruto,
        float $neto,
        float $comision
    ): void {

        // 1. Registro en bóveda (flujo de dinero real)
        VaultService::log(
            'in',
            $bruto,
            "Recaudo Ahorro Socio #{$saving->client->id}",
            $transaction
        );

        // 2. Registro contable (partida doble)
        AccountingService::createEntry(
            "Recaudo Ahorro #{$saving->id}",
            [
                [
                    'account_code' => '101.01',
                    'debit' => $bruto,
                    'credit' => 0,
                ],
                [
                    'account_code' => '201.01',
                    'debit' => 0,
                    'credit' => $neto,
                ],
                [
                    'account_code' => '401.02',
                    'debit' => 0,
                    'credit' => $comision,
                ],
            ],
            $transaction
        );
    }
}