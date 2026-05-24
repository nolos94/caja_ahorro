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
    {{-- Resumen --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="stat-card-mini">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold;">TOTAL ABONADO</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #0284c7;">
                ${{ number_format((float) $payment->amount, 2) }}
            </div>
        </div>
        <div class="stat-card-mini">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold;">MÉTODO DE PAGO</div>
            <div style="font-size: 1.1rem; font-weight: 700;">
                {{ is_object($payment->payment_method) && method_exists($payment->payment_method, 'getLabel') 
                    ? $payment->payment_method->getLabel() 
                    : strtoupper($payment->payment_method) }}
            </div>
        </div>
    </div>

    {{-- Tabla de Detalles --}}
    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;" class="dark:border-gray-700">
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Fecha de Pago</th> {{-- Nueva Columna --}}
                    <th>Cuota #</th>
                    <th>Vencimiento Cuota</th>
                    <th class="text-right">Monto Descontado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->details as $detail)
                    <tr>
                        {{-- Fecha de Pago (Viene del modelo Payment) --}}
                        <td>
                            <span class="text-gray-500">✅</span> 
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                        </td>

                        {{-- Número de Cuota --}}
                        <td style="font-weight: bold; color: #6b7280;">
                            #{{ $detail->installment->installment_number }}
                        </td>

                        {{-- Vencimiento de la Cuota --}}
                        <td>
                            📅 {{ \Carbon\Carbon::parse($detail->installment->due_date)->format('d/m/Y') }}
                        </td>

                        {{-- Valor Descontado --}}
                        <td class="text-right" style="font-weight: bold; color: #0284c7; font-family: monospace;">
                            ${{ number_format((float) $detail->amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>