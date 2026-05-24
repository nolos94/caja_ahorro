<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'dni_ruc', 
        'phone', 
        'address', 
        'credit_limit', 
        'status', 
        'bank_name', 
        'account_number'
    ];

    // Relación inversa: Un cliente pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }
}