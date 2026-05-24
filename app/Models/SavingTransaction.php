<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SavingTransaction extends Model
{

// 1. Asignación masiva (Fillable)
    protected $fillable = [
        'saving_id',
        'type',
        'amount',
        'reference',
        'payment_method',
        'received_by',
    ];

    // 2. Casts para precisión numérica
    protected $casts = [
        'amount' => 'decimal:2',
    ];
// Para la contabilidad y bóveda (Polimorfismo)
    public function vault_movements() {
        return $this->morphMany(VaultTransaction::class, 'source');
    }

    public function ledger_entries() {
        return $this->morphMany(LedgerEntry::class, 'procedencia');
    }

    // Relación con el desglose de cuotas
    public function installmentPayments() {
        return $this->hasMany(SavingInstallmentPayment::class);
    }
}