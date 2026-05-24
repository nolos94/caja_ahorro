<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Hasmany;

class LoanPaymentDetail extends Model
{
    protected $fillable = [
        'loan_payment_id',
        'loan_installment_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relación con el pago global (Cabecera)
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(LoanPayment::class, 'loan_payment_id');
    }

    /**
     * Relación con la cuota específica que se está pagando
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(LoanInstallment::class, 'loan_installment_id');
    }

    /**
 * Relación con los detalles del desglose del pago
 */
public function details(): HasMany
{
    return $this->hasMany(LoanPaymentDetail::class);
}
}