<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Hasmany;
use App\Enums\PaymentMethod;

class LoanPayment extends Model
{
    protected $fillable = [
        'loan_id',
        'loan_installment_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'received_by'
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(LoanInstallment::class, 'loan_installment_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
    public function details(): HasMany
    {
        return $this->hasMany(LoanPaymentDetail::class, 'loan_payment_id');
    }
}