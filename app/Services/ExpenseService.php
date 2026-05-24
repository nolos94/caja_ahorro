<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\DB;
use App\Services\VaultService;
use App\Services\AccountingService;

class ExpenseService
{
    /**
     * Registra un gasto administrativo usando el fondo acumulado.
     */
    public function registerAdminExpense(float $amount, string $description)
    {
        return DB::transaction(function () use ($amount, $description) {
            
            // 1. Validar si hay saldo suficiente en el Fondo de Gastos (Cuenta 401.02)
            $fondoGastos = Account::where('code', '401.02')->first();
            
            if ($fondoGastos->balance < $amount) {
                throw new \Exception("Saldo insuficiente en el Fondo de Gastos Globales. Saldo actual: $" . $fondoGastos->balance);
            }

            // 2. EL CAJERO: Registrar salida física de dinero
            // Esto usa tu VaultService que ya verifica que haya billetes en la caja
            $vaultTransaction = VaultService::log(
                type: 'out',
                amount: $amount,
                concept: "Gasto: " . $description
            );

            // 3. EL CONTADOR: Registro Contable (Partida Doble)
            AccountingService::createEntry(
                concept: "Gasto Administrativo: " . $description,
                procedencia: $vaultTransaction,
                items: [
                    // DEBE: El Gasto aumenta (Cuenta 501.01)
                    [
                        'account_code' => '501.01', 
                        'debit' => $amount, 
                        'credit' => 0
                    ], 
                    
                    // HABER: Disminuye el Fondo de Gastos (Cuenta 401.02)
                    // Nota: Al ser una cuenta de ingreso/patrimonio, el crédito aumenta y el débito disminuye.
                    // Para gastar lo acumulado, "debitamos" la cuenta de ingresos.
                    [
                        'account_code' => '401.02', 
                        'debit' => $amount, 
                        'credit' => 0
                    ],

                    // HABER: Sale el dinero de la Caja General (Cuenta 101.01)
                    [
                        'account_code' => '101.01', 
                        'debit' => 0, 
                        'credit' => $amount
                    ], 
                ]
            );

            return "Gasto registrado con éxito: $" . $amount;
        });
    }
}