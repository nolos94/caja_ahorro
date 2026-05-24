<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'logo',
        'email',
        'phone',
        'address',
        'currency',
        'interest_rate',
        'late_fee',
        'extra'
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public static function getSettings(): self
    {
        return cache()->rememberForever('settings', function () {
            return self::first() ?? self::create();
        });
    }
}
