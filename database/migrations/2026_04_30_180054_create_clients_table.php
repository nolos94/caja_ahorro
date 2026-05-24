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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // Relación obligatoria con la tabla users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Datos del Cliente
            $table->string('dni_ruc')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            
            // Datos Financieros
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            
            // Datos para transferencias
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
