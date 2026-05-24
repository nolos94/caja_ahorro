<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // Ej: 101.01, 201.01
        $table->string('name');           // Ej: Caja General, Ahorros
        $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
        $table->decimal('balance', 15, 2)->default(0); // Saldo actual de la cuenta
        $table->timestamps();
    });

    // 2. Libro Mayor (Asientos Contables)
    Schema::create('ledger_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('account_id')->constrained();
        $table->string('concept');       // Descripción del movimiento
        $table->decimal('debit', 15, 2)->default(0);  // Debe
        $table->decimal('credit', 15, 2)->default(0); // Haber
        
        // Relación polimórfica para saber qué originó este asiento
        $table->nullableMorphs('procedencia'); 
        
        $table->date('entry_date');      // Fecha contable
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_tables');
    }
};
