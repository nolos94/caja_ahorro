<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saving extends Model
{
    protected $fillable = [
        'client_id',
        'balance',
        'status',
    ];

    // 1. CASTS: Para manejar el dinero como decimal y no como string
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // 2. Relación con el Cliente
    public function client(): BelongsTo 
    {
        return $this->belongsTo(Client::class);
    }

    // 3. El "Plan de Ahorro" (Cuotas)
    public function installments(): HasMany 
    {
        return $this->hasMany(SavingInstallment::class);
    }

    // 4. El historial de depósitos/retiros
    public function transactions(): HasMany 
    {
        return $this->hasMany(SavingTransaction::class);
    }

    // EXTRA: Accesor para saber cuánto debe en total (opcional pero útil)
    public function getPendingAmountAttribute()
    {
        return $this->installments()
            ->where('status', '!=', 'paid')
            ->sum('total_amount') - $this->installments()->sum('paid_amount');
    }
}