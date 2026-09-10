<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">
<style>
    *{font-family:DejaVu Sans,sans-serif}
    body{margin:0;color:#1f2937;font-size:11px}
    .head{background:#1e1b4b;color:#fff;padding:16px 20px;text-align:center}
    .head h1{margin:0;font-size:16px}
    .head p{margin:2px 0 0;font-size:10px;color:#c7c1ef}
    .body{padding:18px 20px}
    h3{font-size:12px;text-transform:uppercase;color:#4b5563;border-bottom:1px solid #e5e7eb;padding-bottom:4px;margin:18px 0 8px}
    table{width:100%;border-collapse:collapse;font-size:10.5px;margin-bottom:6px}
    th{background:#f3f4f6;text-align:left;padding:6px 8px;font-size:9.5px;text-transform:uppercase;color:#6b7280}
    td{padding:6px 8px;border-bottom:1px solid #f0f0f0}
    .kpis{display:flex;gap:10px;margin-bottom:6px}
    .kpi{flex:1;border:1px solid #e5e7eb;border-radius:6px;padding:10px;text-align:center}
    .kpi .val{font-size:18px;font-weight:bold}
    .kpi .cap{font-size:9px;color:#6b7280;text-transform:uppercase}
    .aviso{font-size:9px;color:#6b7280;font-style:italic;margin-bottom:10px}
    .foot{margin-top:20px;text-align:center;font-size:9px;color:#9ca3af;border-top:1px dashed #ccc;padding-top:8px}
</style></head><body>
    <div class="head"><h1>{{ $empresa->nombre ?? 'Clínica' }}</h1><p>RESUMEN DEL DÍA — {{ strtoupper($fecha->locale('es')->isoFormat('dddd D [de] MMMM, YYYY')) }}</p></div>
    <div class="body">
        <h3>Agenda del día</h3>
        <div class="kpis">
            <div class="kpi"><div class="val">{{ $totalCitas }}</div><div class="cap">Total citas</div></div>
            <div class="kpi"><div class="val">{{ $atendidas }}</div><div class="cap">Atendidas</div></div>
            <div class="kpi"><div class="val">{{ $pendientes }}</div><div class="cap">Pendientes</div></div>
            <div class="kpi"><div class="val">{{ $canceladas }}</div><div class="cap">Canceladas/No asistió</div></div>
        </div>

        <h3>Cobros del día</h3>
        <table>
            <thead><tr><th>Método</th><th style="text-align:right">Total</th></tr></thead>
            <tbody>
            @forelse($cobrosPorMetodo as $metodo => $monto)
                <tr><td>{{ ucfirst(str_replace('_',' ',$metodo)) }}</td><td style="text-align:right">{{ $empresa->moneda ?? 'S/' }} {{ number_format($monto,2) }}</td></tr>
            @empty
                <tr><td colspan="2" style="text-align:center;color:#9ca3af">Sin pagos registrados ese día.</td></tr>
            @endforelse
                <tr style="font-weight:bold;background:#f9fafb"><td>Total del día</td><td style="text-align:right">{{ $empresa->moneda ?? 'S/' }} {{ number_format($totalCobradoDia,2) }}</td></tr>
            </tbody>
        </table>

        <h3>Deuda pendiente</h3>
        <p class="aviso">Situación actual de la cartera — no corresponde solo a este día, es la deuda acumulada a la fecha.</p>
        <div class="kpis">
            <div class="kpi"><div class="val">{{ $cantidadDeudores }}</div><div class="cap">Pacientes con deuda</div></div>
            <div class="kpi"><div class="val">{{ $empresa->moneda ?? 'S/' }} {{ number_format($totalDeuda,2) }}</div><div class="cap">Deuda total</div></div>
            <div class="kpi"><div class="val">{{ $empresa->moneda ?? 'S/' }} {{ number_format($totalVencido,2) }}</div><div class="cap">Vencido (+60 días)</div></div>
        </div>

        <div class="foot">Generado el {{ now()->format('d/m/Y H:i') }} · Documento interno de gestión</div>
    </div>
</body></html>