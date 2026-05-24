<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingInstallmentPayment extends Model
{
    protected $fillable = [
        'saving_transaction_id', 
        'saving_installment_id', 
        'amount'
    ];

    // 1. CASTS: Crucial para no perder centavos en cálculos matemáticos
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // 2. RELACIONES CON TIPADO (Mejor para el autocompletado de tu IDE)
    public function transaction(): BelongsTo 
    {
        return $this->belongsTo(SavingTransaction::class, 'saving_transaction_id');
    }

    public function installment(): BelongsTo 
    {
        return $this->belongsTo(SavingInstallment::class, 'saving_installment_id');
    }
}