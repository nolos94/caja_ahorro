<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LedgerEntry extends Model
{
    //
    protected $fillable = [
        'account_id', 'concept', 'debit', 'credit', 
        'procedencia_id', 'procedencia_type', 'entry_date'
    ];

    public function procedencia(): MorphTo
    {
        return $this->morphTo();
    }
}
