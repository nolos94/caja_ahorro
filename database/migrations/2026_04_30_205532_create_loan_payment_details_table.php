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
        Schema::create('loan_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_installment_id')->constrained();
            $table->decimal('amount', 15, 2); // Cuánto de este pago se fue a esta cuota
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payment_details');
    }
};
