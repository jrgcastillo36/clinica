<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
<style>
    *{font-family:DejaVu Sans,sans-serif;}
    body{color:#1f2937;font-size:12px;margin:0}
    .head{background:#1e1b4b;color:#fff;padding:14px 18px}
    .head h1{margin:0;font-size:16px}
    .head p{margin:2px 0 0;font-size:10.5px;color:#c7c1ef}
    .body{padding:16px 20px}
    table.data{width:100%;border-collapse:collapse;margin-bottom:14px}
    table.data td{padding:7px 4px;border-bottom:1px solid #eee}
    table.data td:last-child{text-align:right;font-weight:bold}
    .total-row td{font-weight:bold;background:#fdf2fb;border-top:2px solid #ec4899}
    .diferencia{font-weight:bold;font-size:14px;text-align:center;padding:10px;border-radius:8px;margin-top:6px}
    .ok{background:#dcfce7;color:#166534}
    .warn{background:#fef3c7;color:#92400e}
    .firma{margin-top:40px;display:flex;justify-content:space-between}
    .firma div{width:45%;text-align:center;border-top:1px solid #333;padding-top:4px;font-size:10px}
</style></head><body>
    <div class="head">
        <h1>{{ $empresa->nombre ?? 'Clínica' }} — Cierre de Caja</h1>
        <p>Fecha: {{ $cierre->fecha->locale('es')->isoFormat('D [de] MMMM, YYYY') }} · Generado {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="body">
        <table class="data">
            <tr><td>Fondo inicial</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($cierre->efectivo_inicial,2) }}</td></tr>
            <tr><td>Total cobrado (todos los métodos)</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($cierre->total_sistema,2) }}</td></tr>
            <tr><td>Efectivo cobrado hoy</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($cierre->efectivo_sistema,2) }}</td></tr>
            <tr class="total-row"><td>Efectivo esperado (fondo + efectivo cobrado)</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($cierre->efectivo_esperado,2) }}</td></tr>
            <tr><td>Efectivo contado físicamente</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($cierre->efectivo_contado,2) }}</td></tr>
        </table>

        <div class="diferencia {{ $cierre->diferencia == 0 ? 'ok' : 'warn' }}">
            @if($cierre->diferencia == 0)
                ✓ Caja cuadrada — sin diferencia
            @else
                Diferencia: {{ $empresa->moneda ?? 'S/' }} {{ number_format($cierre->diferencia,2) }}
                ({{ $cierre->diferencia > 0 ? 'sobrante' : 'faltante' }})
            @endif
        </div>

        @if($cierre->observaciones)
            <p style="margin-top:14px"><b>Observaciones:</b><br>{{ $cierre->observaciones }}</p>
        @endif

        <p style="margin-top:20px;font-size:10.5px;color:#6b7280">
            Caja abierta por: {{ $cierre->abiertoPor->name ?? '—' }}<br>
            Caja cerrada por: {{ $cierre->usuario->name ?? '—' }} a las {{ optional($cierre->cerrado_at)->format('H:i') }}
        </p>

        <div class="firma">
            <div>Firma responsable de caja</div>
            <div>Firma supervisor / admin</div>
        </div>
    </div>
</body></html>
