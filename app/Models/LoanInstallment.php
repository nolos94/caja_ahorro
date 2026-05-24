<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\InstallmentStatus;

class LoanInstallment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'paid_amount',
        'status'
    ];

    protected $casts = [
        'status' => InstallmentStatus::class,
        'due_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Opcional: Una cuota puede tener varios abonos si el pago es parcial
     */
    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class, 'loan_installment_id');
    }
}