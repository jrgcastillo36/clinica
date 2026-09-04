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
    .kpis td{border:1px solid #e5e7eb;padding:10px;text-align:center;width:25%}
    .kpis .n{font-size:17px;font-weight:bold;color:#7c3aed}
    .kpis .l{font-size:8.5px;color:#6b7280;text-transform:uppercase}
    h3{font-size:13px;border-bottom:2px solid #ec4899;padding-bottom:4px;margin-top:20px}
    table.data{width:100%;border-collapse:collapse;margin-top:6px}
    table.data th{background:#fdf2fb;text-align:left;padding:7px;font-size:10px;text-transform:uppercase;color:#6b7280}
    table.data td{padding:7px;border-bottom:1px solid #eee}
    .cols2{width:100%}
    .cols2 td{vertical-align:top;width:50%;padding-right:14px}
</style></head><body>
    <div class="head">
        <h1>{{ $empresa->nombre ?? 'Clínica' }} — Reporte Clínico</h1>
        <p>Generado {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="body">
        <table class="kpis"><tr>
            <td><div class="n">{{ $totalPacientes }}</div><div class="l">Pacientes registrados</div></td>
            <td><div class="n">{{ $totalConsultas }}</div><div class="l">Consultas totales</div></td>
            <td><div class="n">{{ $satisfaccion }}/5</div><div class="l">Satisfacción promedio</div></td>
            <td><div class="n">{{ $totalEncuestas }}</div><div class="l">Encuestas respondidas</div></td>
        </tr></table>

        <table class="cols2"><tr>
            <td>
                <h3>Diagnósticos más frecuentes</h3>
                <table class="data"><tr><th>Diagnóstico</th><th>N°</th></tr>
                    @forelse($diagnosticos as $diag => $c)<tr><td>{{ $diag }}</td><td>{{ $c }}</td></tr>
                    @empty <tr><td colspan="2">Sin diagnósticos registrados</td></tr>@endforelse
                </table>
            </td>
            <td>
                <h3>Distribución por sexo</h3>
                <table class="data"><tr><th>Sexo</th><th>N°</th></tr>
                    @foreach($sexo as $s => $c)<tr><td>{{ $s }}</td><td>{{ $c }}</td></tr>@endforeach
                </table>
            </td>
        </tr></table>

        <h3>Distribución por edad</h3>
        <table class="data"><tr><th>Rango</th><th>N°</th></tr>
            @foreach($edades as $r => $c)<tr><td>{{ $r }} años</td><td>{{ $c }}</td></tr>@endforeach
        </table>
    </div>
</body></html>
