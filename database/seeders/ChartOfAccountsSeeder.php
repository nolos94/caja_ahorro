<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $accounts = [
        ['code' => '101.01', 'name' => 'Caja General', 'type' => 'asset'],
        ['code' => '102.01', 'name' => 'Cartera de Préstamos', 'type' => 'asset'],
        ['code' => '201.01', 'name' => 'Ahorros de Socios', 'type' => 'liability'],
        ['code' => '401.01', 'name' => 'Ingresos por Intereses', 'type' => 'revenue'],
        ['code' => '401.02', 'name' => 'Fondo Gastos Globales (5%)', 'type' => 'revenue'],
        ['code' => '501.01', 'name' => 'Gastos Administrativos', 'type' => 'expense'],
    ];

    foreach ($accounts as $acc) {
        \App\Models\Account::updateOrCreate(['code' => $acc['code']], $acc);
    }
}
}
