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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    
            // Configuración del crédito

            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('installments_count');
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly']);
            
            // Totales para reportes rápidos
            $table->decimal('total_interest', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('balance', 15, 2); 
            
            // Estados y Aprobación
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'active', 'completed', 'defaulted', 'cancelled'])
                ->default('draft');
            
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('disbursement_date')->nullable(); // Se llena al pasar a 'active'
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
