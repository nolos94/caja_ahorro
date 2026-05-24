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
        Schema::create('saving_installment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_transaction_id')
                ->constrained()
                ->name('s_t_id_foreign')
                ->cascadeOnDelete();
            $table->foreignId('saving_installment_id')
                ->constrained()
                ->name('s_i_id_foreign')
                ->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saving_installment_payments');
    }
};
