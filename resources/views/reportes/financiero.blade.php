@extends('layouts.app')
@section('title', 'Reporte financiero')

@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Reporte financiero</h1><p>Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</p></div>
<div class="flex gap" style="flex-wrap:wrap">
        <a href="{{ route('reportes.financiero.pdf', ['desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()]) }}" target="_blank" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> Exportar PDF</a>
            <a href="{{ route('reportes.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Reportes</a>
        </div>
    </div>

    <form method="GET" class="card mb" style="padding:14px">
        <div class="flex gap" style="flex-wrap:wrap;align-items:flex-end">
            <div class="field"><label>Desde</label><input type="date" name="desde" value="{{ $desde->toDateString() }}"></div>
            <div class="field"><label>Hasta</label><input type="date" name="hasta" value="{{ $hasta->toDateString() }}"></div>
            <button class="btn btn-primary">Aplicar</button>
        </div>
    </form>

       <div class="grid g-3 mb">
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Ingresos (periodo)</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#15803d">@money($total, null, 2)</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">N° de pagos</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $numPagos }}</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Ticket promedio</div><div style="font-size:26px;font-weight:700;margin-top:6px">@money($ticket, null, 2)</div></div>
    </div>

    <div class="grid g-3 mb">
        <div class="card" style="background:{{ $totalPendiente > 0 ? '#fef3c7' : '#f1f5f9' }}">
            <div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Total pendiente de cobro (a la fecha)</div>
            <div style="font-size:26px;font-weight:700;margin-top:6px;color:{{ $totalPendiente > 0 ? '#92400e' : '#475569' }}">@money($totalPendiente, null, 2)</div>
        </div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Tasa de cobro</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:{{ $tasaCobro >= 80 ? '#15803d' : '#b45309' }}">{{ $tasaCobro }}%</div></div>
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Pacientes con deuda</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $deudores->count() }}</div></div>
    </div>

    <div class="grid g-2 mb">
        <div class="card"><h3 class="mb">Ingresos por mes</h3><div class="chart-box"><canvas id="chMes"></canvas></div></div>
        <div class="card"><h3 class="mb">Por método de pago</h3><div class="chart-box"><canvas id="chMet"></canvas></div></div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Top pacientes por gasto</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Paciente</th><th>Total gastado</th></tr></thead>
                <tbody>
                @forelse($topPacientes as $t)
                    <tr><td>{{ $t->paciente->nombre_completo ?? '—' }}</td><td><b>@money($t->total, null, 2)</b></td></tr>
                @empty
                    <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-coins"></i><p>Sin pagos en el periodo.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="grid g-2 mb">
        <div class="card" style="padding:0">
            <div style="padding:18px 22px 8px"><h3 style="margin:0">Pacientes con saldo pendiente</h3></div>
            <div class="table-wrap" style="box-shadow:none;border-radius:0">
                <table>
                    <thead><tr><th>Paciente</th><th>Debe</th></tr></thead>
                    <tbody>
                    @forelse($deudores as $d)
                        <tr><td>{{ $d['nombre'] }}</td><td><b style="color:#b45309">@money($d['monto'], null, 2)</b></td></tr>
                    @empty
                        <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-circle-check"></i><p>Nadie tiene saldo pendiente 🎉</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card" style="padding:0">
            <div style="padding:18px 22px 8px"><h3 style="margin:0">Ingresos por servicio (periodo)</h3></div>
            <div class="table-wrap" style="box-shadow:none;border-radius:0">
                <table>
                    <thead><tr><th>Servicio</th><th>Total cobrado</th></tr></thead>
                    <tbody>
                    @forelse($porServicio as $nombre => $monto)
                        <tr><td>{{ $nombre }}</td><td><b>@money($monto, null, 2)</b></td></tr>
                    @empty
                        <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-briefcase-medical"></i><p>Sin pagos con servicio asignado en el periodo.</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
    window.addEventListener('load', function(){
        if(!window.Chart) return;
        const g=document.getElementById('chMes').getContext('2d').createLinearGradient(0,0,0,230);
        g.addColorStop(0,'#a855f7'); g.addColorStop(1,'#ec4899');
        new Chart(document.getElementById('chMes'), {
            type:'bar', data:{ labels:@json($labels), datasets:[{ data:@json($serie), backgroundColor:g, borderRadius:10 }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
        });
        new Chart(document.getElementById('chMet'), {
            type:'doughnut',
            data:{ labels:@json($porMetodo->pluck('metodo')), datasets:[{ data:@json($porMetodo->pluck('total')),
                backgroundColor:['#ec4899','#a855f7','#7c3aed','#06b6d4','#f59e0b'] }] },
            options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
        });
    });
    </script>
    @endpush
@endsection
