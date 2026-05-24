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
        Schema::create('vault_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out']); // Entrada o Salida de dinero
            $table->decimal('amount', 15, 2);
            $table->string('concept'); // Ej: "Ahorro Socio #1", "Desembolso Préstamo #5"
            // Relación polimórfica para saber de dónde viene el movimiento
            $table->nullableMorphs('source'); 
            $table->decimal('current_vault_balance', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vault_transactions');
    }
};
