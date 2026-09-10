@extends('layouts.app')
@section('title', 'Resumen del día')

@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Resumen del día</h1><p>{{ $fecha->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}</p></div>
        <div class="flex gap">
            <a href="{{ route('reportes.resumen-diario.pdf', ['fecha' => $fecha->toDateString()]) }}" target="_blank" class="btn btn-primary"><i class="fa-solid fa-file-pdf"></i> Exportar PDF</a>
            <a href="{{ route('reportes.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Reportes</a>
        </div>
    </div>

    <form method="GET" class="card mb" style="padding:14px;max-width:300px">
        <div class="field">
            <label>Elegir fecha</label>
            <input type="date" name="fecha" value="{{ $fecha->toDateString() }}" onchange="this.form.submit()">
        </div>
    </form>

    <h3 class="mb">Agenda de {{ $fecha->isToday() ? 'hoy' : 'ese día' }}</h3>
    <div class="grid g-4 mb">
        <div class="kpi k1"><div class="kpi-val">{{ $totalCitas }}</div><div class="kpi-cap">Total citas</div></div>
        <div class="kpi k3"><div class="kpi-val">{{ $atendidas }}</div><div class="kpi-cap">Atendidas</div></div>
        <div class="kpi k2"><div class="kpi-val">{{ $pendientes }}</div><div class="kpi-cap">Pendientes</div></div>
        <div class="kpi k4"><div class="kpi-val">{{ $canceladas }}</div><div class="kpi-cap">Canceladas / No asistió</div></div>
    </div>

    <h3 class="mb">Cobros de {{ $fecha->isToday() ? 'hoy' : 'ese día' }}</h3>
    <div class="table-wrap mb">
        <table>
            <thead><tr><th>Método</th><th>Total</th></tr></thead>
            <tbody>
            @forelse($cobrosPorMetodo as $metodo => $monto)
                <tr><td>{{ ucfirst(str_replace('_',' ',$metodo)) }}</td><td><b>{{ $mon }} {{ number_format($monto,2) }}</b></td></tr>
            @empty
                <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-money-bill"></i><p>Sin pagos registrados ese día.</p></div></td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr style="font-weight:700;background:var(--bg-pink)"><td>Total del día</td><td>{{ $mon }} {{ number_format($totalCobradoDia,2) }}</td></tr>
            </tfoot>
        </table>
    </div>

    <h3 class="mb">Deuda pendiente <small class="muted" style="font-weight:400;font-size:12px">(situación actual, no depende de la fecha elegida)</small></h3>
    <div class="grid g-3 mb">
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Pacientes con deuda</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $cantidadDeudores }}</div></div>
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Deuda total</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#b45309">{{ $mon }} {{ number_format($totalDeuda,2) }}</div></div>
        <div class="card" style="background:#fee2e2"><div class="cap" style="font-size:12px;color:#991b1b;text-transform:uppercase">Vencido (+60 días)</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#991b1b">{{ $mon }} {{ number_format($totalVencido,2) }}</div></div>
    </div>
    <a href="{{ route('pagos.estados') }}" class="btn btn-light btn-sm"><i class="fa-solid fa-list"></i> Ver detalle completo en Estado de cuenta</a>
@endsection