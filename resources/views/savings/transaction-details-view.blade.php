<style>
    .payment-table { width: 100%; border-collapse: collapse; }
    .payment-table th { 
        background: #f3f4f6; color: #374151; font-weight: 700; text-transform: uppercase; 
        font-size: 0.75rem; padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;
    }
    .payment-table td { padding: 12px; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; }
    .dark .payment-table td { border-bottom-color: #374151; color: #d1d5db; }
    .dark .payment-table th { background: #1f2937; color: #f3f4f6; border-bottom-color: #4b5563; }
    .text-right { text-align: right; }
    .stat-card-mini {
        background: #ffffff; border: 1px solid #e5e7eb; padding: 16px; border-radius: 12px; text-align: center;
    }
    .dark .stat-card-mini { background: #111827; border-color: #374151; }
</style>

<div class="space-y-6">
    {{-- Resumen de la Transacción --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="stat-card-mini">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold; text-transform: uppercase;">MONTO DEPOSITE</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #10b981;">
                ${{ number_format((float) $transaction->amount, 2) }}
            </div>
        </div>
        <div class="stat-card-mini">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold; text-transform: uppercase;">MÉTODO / REF</div>
            <div style="font-size: 1.1rem; font-weight: 700; color: #374151;" class="dark:text-gray-200">
                {{ strtoupper($transaction->payment_method) }} 
                <span style="font-size: 0.8rem; color: #9ca3af; font-weight: normal;">
                    ({{ $transaction->reference ?? 'S/R' }})
                </span>
            </div>
        </div>
    </div>

    {{-- Tabla de Distribución a Cuotas --}}
    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;" class="dark:border-gray-700">
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Fecha Registro</th>
                    <th>ID Cuota</th>
                    <th>Periodo (Mes/Año)</th>
                    <th class="text-right">Abono Aplicado</th>
                </tr>
            </thead>
            <tbody>
                {{-- USANDO LA RELACIÓN DEFINIDA EN EL MODELO: installmentPayments --}}
                @forelse($transaction->installmentPayments as $item)
                    <tr>
                        <td>
                            <span class="text-green-500">💰</span> 
                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td style="font-weight: bold; color: #6b7280;">
                            #{{ $item->saving_installment_id }}
                        </td>

                        <td>
                            📅 {{ $item->installment->month_year ?? 'N/A' }}
                        </td>

                        <td class="text-right" style="font-weight: bold; color: #10b981; font-family: monospace;">
                            ${{ number_format((float) $item->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #9ca3af;">
                            No hay detalles de distribución para este movimiento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            {{-- VALIDACIÓN Y SUMA USANDO EL NOMBRE CORRECTO --}}
            @if($transaction->installmentPayments && count($transaction->installmentPayments) > 0)
            <tfoot>
                <tr style="background: #f9fafb;" class="dark:bg-gray-800/50">
                    <td colspan="3" class="text-right" style="font-weight: bold; font-size: 0.75rem; color: #6b7280;">TOTAL DISTRIBUIDO:</td>
                    <td class="text-right" style="font-weight: 800; color: #10b981; font-size: 1rem; font-family: monospace;">
                        ${{ number_format((float) $transaction->installmentPayments->sum('amount'), 2) }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>