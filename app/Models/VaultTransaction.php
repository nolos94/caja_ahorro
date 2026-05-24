<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VaultTransaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'concept',
        'source_id',
        'source_type',
        'current_vault_balance',
    ];

    // Para la relación polimórfica (saber qué originó el movimiento)
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}