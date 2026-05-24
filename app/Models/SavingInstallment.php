<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SavingInstallment extends Model
{
    // 1. IMPORTANTE: Permitir el llenado de datos
    protected $fillable = [
        'saving_id',
        'month_year',
        'saving_amount',
        'fee_amount',
        'total_amount',
        'paid_amount',
        'status',
    ];

    // 2. CASTS: Para que Laravel trate los números y fechas correctamente
    protected $casts = [
        'saving_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // 3. Relación con la Cuenta de Ahorro (Padre)
public function account(): BelongsTo 
{
    return $this->belongsTo(Saving::class, 'saving_id');
}

    // --- Relaciones existentes mejoradas con tipos ---

    public function installmentPayments(): HasMany 
    {
        return $this->hasMany(SavingInstallmentPayment::class);
    }

    public function vault_movements(): MorphMany 
    {
        return $this->morphMany(VaultTransaction::class, 'source');
    }

    public function ledger_entries(): MorphMany 
    {
        return $this->morphMany(LedgerEntry::class, 'procedencia');
    }
}