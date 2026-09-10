<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">
<style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:11px}
    .head{background:#1e1b4b;color:#fff;padding:16px 20px;text-align:center}
    .head h1{margin:0;font-size:16px}
    .head p{margin:2px 0 0;font-size:10px;color:#c7c1ef}
    .body{padding:18px 20px}
    .info{display:flex;justify-content:space-between;margin-bottom:14px;font-size:10.5px}
    .info .lbl{color:#6b7280;font-size:9px;text-transform:uppercase}
    table.mov{width:100%;border-collapse:collapse;font-size:10.5px}
    table.mov th{background:#f3f4f6;text-align:left;padding:6px 8px;font-size:9.5px;text-transform:uppercase;color:#6b7280}
    table.mov td{padding:6px 8px;border-bottom:1px solid #f0f0f0}
    .cargo{color:#991b1b}
    .pago{color:#15803d}
    .resumen{margin-top:16px;display:flex;justify-content:flex-end}
    .resumen table{font-size:11px}
    .resumen td{padding:3px 10px}
    .resumen .final{font-weight:bold;font-size:13px;border-top:1px solid #1e1b4b;padding-top:6px}
    .foot{margin-top:20px;text-align:center;font-size:9px;color:#9ca3af;border-top:1px dashed #ccc;padding-top:8px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }}</h1><p>ESTADO DE CUENTA</p></div>
    <div class="body">
        <div class="info">
            <div>
                <div class="lbl">Paciente</div>
                <div><b>{{ $paciente->nombre_completo }}</b></div>
                <div>{{ $paciente->tipo_documento }} {{ $paciente->documento ?? '—' }}</div>
            </div>
            <div style="text-align:right">
                <div class="lbl">Generado el</div>
                <div>{{ $generadoEl->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <table class="mov">
            <thead><tr><th>Fecha</th><th>Descripción</th><th style="text-align:right">Cargo</th><th style="text-align:right">Pago</th><th style="text-align:right">Saldo</th></tr></thead>
            <tbody>
            @forelse($movimientos as $m)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($m['fecha'])->format('d/m/Y') }}</td>
                    <td>{{ $m['descripcion'] }}</td>
                    <td style="text-align:right" class="cargo">{{ $m['tipo'] === 'cargo' ? number_format($m['monto'],2) : '' }}</td>
                    <td style="text-align:right" class="pago">{{ $m['tipo'] === 'pago' ? number_format(abs($m['monto']),2) : '' }}</td>
                    <td style="text-align:right"><b>{{ $empresa->moneda ?? 'S/' }} {{ number_format($m['saldo'],2) }}</b></td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:20px">Sin movimientos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="resumen">
            <table>
                <tr><td>Total cargos</td><td style="text-align:right">{{ $empresa->moneda ?? 'S/' }} {{ number_format($totalCargos,2) }}</td></tr>
                <tr><td>Total pagado</td><td style="text-align:right">{{ $empresa->moneda ?? 'S/' }} {{ number_format($totalPagos,2) }}</td></tr>
                <tr class="final"><td>Saldo {{ $saldoFinal > 0 ? 'pendiente' : 'al día' }}</td><td style="text-align:right">{{ $empresa->moneda ?? 'S/' }} {{ number_format($saldoFinal,2) }}</td></tr>
            </table>
        </div>

        <div class="foot">Documento informativo, no constituye comprobante fiscal · {{ $empresa->telefono ?? '' }}</div>
    </div>
</body></html>