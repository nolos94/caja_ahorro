<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\LoanStatus;
use App\Enums\LoanFrequency;
use App\Enums\AmortizationMethod;

class Loan extends Model
{
    protected $fillable = [
        'client_id',
        'amortization_method',
        'amount',
        'interest_rate',
        'installments_count',
        'frequency',
        'total_interest',
        'total_amount',
        'balance',
        'status',
        'approved_at',
        'approved_by',
        'disbursement_date',
        'notes'
    ];

    protected $casts = [
        'amortization_method'=>AmortizationMethod::class,
        'status' => LoanStatus::class,
        'frequency' => LoanFrequency::class,
        'approved_at' => 'datetime',
        'disbursement_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}