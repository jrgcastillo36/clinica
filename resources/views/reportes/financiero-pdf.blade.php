<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
<style>
    *{font-family:DejaVu Sans,sans-serif;}
    body{color:#1f2937;font-size:12px;margin:0}
    .head{background:#1e1b4b;color:#fff;padding:16px 22px}
    .head h1{margin:0;font-size:18px}
    .head p{margin:2px 0 0;font-size:11px;color:#c7c1ef}
    .body{padding:20px 24px}
    .kpis{width:100%;margin-bottom:18px}
    .kpis td{border:1px solid #e5e7eb;padding:10px;text-align:center;width:20%}
    .kpis .n{font-size:17px;font-weight:bold;color:#7c3aed}
    .kpis .l{font-size:8.5px;color:#6b7280;text-transform:uppercase}
    .kpis .warn .n{color:#b45309}
    h3{font-size:13px;border-bottom:2px solid #ec4899;padding-bottom:4px;margin-top:20px}
    table.data{width:100%;border-collapse:collapse;margin-top:6px}
    table.data th{background:#fdf2fb;text-align:left;padding:7px;font-size:10px;text-transform:uppercase;color:#6b7280}
    table.data td{padding:7px;border-bottom:1px solid #eee}
    .deuda{color:#b45309;font-weight:bold}
</style></head><body>
    <div class="head">
        <h1>{{ $empresa->nombre ?? 'Clínica' }} — Reporte Financiero</h1>
        <p>Periodo: {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }} · Generado {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="body">
        <table class="kpis"><tr>
            <td><div class="n">{{ $empresa->moneda ?? 'S/' }} {{ number_format($total,2) }}</div><div class="l">Ingresos periodo</div></td>
            <td><div class="n">{{ $numPagos }}</div><div class="l">N° de pagos</div></td>
            <td><div class="n">{{ $empresa->moneda ?? 'S/' }} {{ number_format($ticket,2) }}</div><div class="l">Ticket promedio</div></td>
            <td class="warn"><div class="n">{{ $empresa->moneda ?? 'S/' }} {{ number_format($totalPendiente,2) }}</div><div class="l">Pendiente de cobro</div></td>
            <td><div class="n">{{ $tasaCobro }}%</div><div class="l">Tasa de cobro</div></td>
        </tr></table>

        <h3>Pagos por método</h3>
        <table class="data"><tr><th>Método</th><th>N° pagos</th><th>Total</th></tr>
            @forelse($porMetodo as $m)<tr><td>{{ ucfirst(str_replace('_',' ',$m->metodo)) }}</td><td>{{ $m->c }}</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($m->total,2) }}</td></tr>
            @empty <tr><td colspan="3">Sin pagos en el periodo</td></tr>@endforelse
        </table>

        <h3>Pacientes con saldo pendiente</h3>
        <table class="data"><tr><th>Paciente</th><th>Debe</th></tr>
            @forelse($deudores as $d)<tr><td>{{ $d['nombre'] }}</td><td class="deuda">{{ $empresa->moneda ?? 'S/' }} {{ number_format($d['monto'],2) }}</td></tr>
            @empty <tr><td colspan="2">Nadie tiene saldo pendiente</td></tr>@endforelse
        </table>

        <h3>Ingresos por servicio (periodo)</h3>
        <table class="data"><tr><th>Servicio</th><th>Total cobrado</th></tr>
            @forelse($porServicio as $nombre => $monto)<tr><td>{{ $nombre }}</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($monto,2) }}</td></tr>
            @empty <tr><td colspan="2">Sin pagos con servicio asignado</td></tr>@endforelse
        </table>

        <h3>Top pacientes por gasto</h3>
        <table class="data"><tr><th>Paciente</th><th>Total gastado</th></tr>
            @forelse($topPacientes as $t)<tr><td>{{ $t->paciente->nombre_completo ?? '—' }}</td><td>{{ $empresa->moneda ?? 'S/' }} {{ number_format($t->total,2) }}</td></tr>
            @empty <tr><td colspan="2">Sin datos</td></tr>@endforelse
        </table>
    </div>
</body></html>
