<style>
    .amortization-table { width: 100%; border-collapse: collapse; font-family: sans-serif; }
    .amortization-table th { 
        background: #f3f4f6; color: #374151; font-weight: 700; text-transform: uppercase; 
        font-size: 0.75rem; padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;
    }
    .amortization-table td { padding: 12px; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; color: #1f2937; }
    .dark .amortization-table td { color: #d1d5db; border-bottom-color: #374151; }
    .dark .amortization-table th { background: #1f2937; color: #f3f4f6; border-bottom-color: #4b5563; }
    
    .stat-card {
        background: #ffffff; border: 1px solid #e5e7eb; padding: 16px; border-radius: 12px;
        text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .dark .stat-card { background: #111827; border-color: #374151; }
    
    .text-mono { font-family: ui-monospace, monospace; }
    .text-right { text-align: right; }
    .text-primary { color: #0284c7; }
    .text-amber { color: #d97706; }
</style>

<div class="space-y-6">
    {{-- Resumen --}}
    @php
        $data = collect($installments);
        $totalPagar = $data->sum(fn ($i) => (float) data_get($i, 'total_amount'));
        $totalInteres = $data->sum(fn ($i) => (float) data_get($i, 'interest_amount'));
    @endphp

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        <div class="stat-card">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold;">TOTAL A PAGAR</div>
            <div style="font-size: 1.5rem; font-weight: 800;" class="text-primary">${{ number_format($totalPagar, 2) }}</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold;">INTERESES</div>
            <div style="font-size: 1.5rem; font-weight: 800;" class="text-amber">${{ number_format($totalInteres, 2) }}</div>
        </div>
        <div class="stat-card">
            <div style="font-size: 0.75rem; color: #6b7280; font-weight: bold;">CUOTAS</div>
            <div style="font-size: 1.5rem; font-weight: 800;">{{ count($installments) }}</div>
        </div>
    </div>

    {{-- Tabla --}}
    <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;" class="dark:border-gray-700">
        <table class="amortization-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha de Pago</th>
                    <th class="text-right">Capital</th>
                    <th class="text-right">Interés</th>
                    <th class="text-right text-primary">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installments as $index => $item)
                    <tr>
                        <td class="text-mono" style="color: #6b7280;">{{ data_get($item, 'installment_number', $index + 1) }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                📅 {{ \Carbon\Carbon::parse(data_get($item, 'due_date'))->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="text-right text-mono">${{ number_format((float) data_get($item, 'principal_amount'), 2) }}</td>
                        <td class="text-right text-mono text-amber">${{ number_format((float) data_get($item, 'interest_amount'), 2) }}</td>
                        <td class="text-right text-mono" style="font-weight: bold; color: #0284c7;">
                            ${{ number_format((float) data_get($item, 'total_amount'), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>