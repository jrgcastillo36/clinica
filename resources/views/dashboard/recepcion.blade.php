@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Recepción 🪷</h1>...<p>{{ now()->locale('es')->isoFormat('dddd D [de] MMMM') }}</p></div>
        <div class="flex gap">
{{-- <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Nueva cita</a> --}}
        </div>
    </div>
    @if($citasMananaSinConfirmar > 0)
    <div class="alert" style="background:#fffbeb;color:#92400e;margin-bottom:18px">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Tienes <b>{{ $citasMananaSinConfirmar }}</b> {{ $citasMananaSinConfirmar === 1 ? 'cita' : 'citas' }} de mañana sin confirmar todavía.
        <a href="{{ route('citas.index', ['estado' => 'pendiente']) }}" style="margin-left:auto;color:#92400e;font-weight:600;text-decoration:underline">Ver</a>
    </div>
    @endif
       <div class="grid g-4 mb">
        <div class="kpi k1">
            <div class="kpi-top"><span class="kpi-ic"><i class="fa-solid fa-calendar-day"></i></span></div>
            <div class="kpi-val">{{ $citasHoy }}</div>
            <div class="kpi-cap">Citas hoy</div>
        </div>
        <div class="kpi k2">
            <div class="kpi-top"><span class="kpi-ic"><i class="fa-solid fa-hourglass-half"></i></span></div>
            <div class="kpi-val">{{ $pendientes }}</div>
            <div class="kpi-cap">Por confirmar</div>
        </div>
        <div class="kpi k3">
            <div class="kpi-top"><span class="kpi-ic"><i class="fa-solid fa-sack-dollar"></i></span></div>
            <div class="kpi-val">@money($cobradoHoy, null, 2)</div>
            <div class="kpi-cap">Cobrado hoy</div>
        </div>
        <div class="kpi k4">
            <div class="kpi-top"><span class="kpi-ic"><i class="fa-solid fa-triangle-exclamation"></i></span></div>
            <div class="kpi-val">{{ $pagosPendientes }}</div>
            <div class="kpi-cap">Pagos pendientes</div>
        </div>
    </div>

    <div class="card" style="padding:0">
        <div style="padding:18px 22px 8px"><h3 style="margin:0">Agenda de hoy</h3></div>
        <div class="table-wrap" style="box-shadow:none;border-radius:0">
            <table>
                <thead><tr><th>Hora</th><th>Paciente</th><th>Especialidad</th><th>Psicólogo(a)</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($agendaHoy as $c)
                    <tr>
                        <td><b>{{ \Illuminate\Support\Str::of($c->hora)->substr(0,5) }}</b></td>
                        <td>{{ $c->paciente->nombre_completo }}</td>
                        <td>{{ $c->especialidad->nombre ?? '—' }}</td>
                        <td>{{ $c->medico->name ?? '—' }}</td>
                        <td>@include('citas.estado', ['estado' => $c->estado])</td>
                        <td style="text-align:right;white-space:nowrap">
                            @if($c->estado === 'pendiente')
                            <form method="POST" action="{{ route('citas.estado.cambiar', $c) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="estado" value="confirmada">
                                <button class="btn btn-light btn-sm"><i class="fa-solid fa-check"></i> Confirmar</button>
                            </form>
                            @endif
                            @php
                                $yaPaso = \Carbon\Carbon::parse($c->fecha->format('Y-m-d').' '.$c->hora)->isPast();
                            @endphp
                            @if($yaPaso && in_array($c->estado, ['pendiente', 'confirmada']))
                            <form method="POST" action="{{ route('citas.estado.cambiar', $c) }}" style="display:inline" onsubmit="return confirm('¿Marcar como No asistió?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="estado" value="no_asistio">
                                <button class="btn btn-light btn-sm" style="color:#94a3b8"><i class="fa-solid fa-user-xmark"></i> No asistió</button>
                            </form>
                            @endif
@php
    $consulta = $c->consulta;
    $estadoCobro = 'pendiente';
    $montoACobrar = null;
    $montoRegistrado = 0;

    if ($consulta && $consulta->servicio_id) {
        $pagado = $consulta->pago->sum('monto');
        $precio = $consulta->servicio->precio ?? 0;
        $saldoRestante = max($precio - $pagado, 0);
        if ($saldoRestante <= 0) {
            $estadoCobro = 'cobrado';
        } else {
            $montoACobrar = $saldoRestante;
        }
    } else {
        $pagosDirectos = $c->pagos->where('servicio_id', '!=', null);
        if ($pagosDirectos->isNotEmpty()) {
            $primero = $pagosDirectos->first();
            $precio = (float) ($primero->servicio->precio ?? 0);
            $pagado = $pagosDirectos->sum('monto');
            $saldoRestante = max($precio - $pagado, 0);
            if ($saldoRestante <= 0) {
                $estadoCobro = 'cobrado';
            } else {
                $estadoCobro = 'registrado';
                $montoACobrar = $saldoRestante;
                $montoRegistrado = $pagado;
            }
        } elseif ($c->pagos->isNotEmpty()) {
            $estadoCobro = 'registrado';
            $montoRegistrado = $c->pagos->sum('monto');
        }
    }
@endphp
@if($estadoCobro === 'cobrado')
    <span class="pill" style="background:#dcfce7;color:#166534"><i class="fa-solid fa-check"></i> Cobrado</span>
@else
    @if($estadoCobro === 'registrado')
        <span class="pill" style="background:#fef3c7;color:#92400e;margin-right:6px;font-size:10.5px">Ya: {{ $mon }}{{ number_format($montoRegistrado,2) }}</span>
    @endif
    <a href="{{ route('pagos.create', ['paciente_id' => $c->paciente_id, 'cita_id' => $c->id]) }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-money-bill"></i> Cobrar{{ $montoACobrar !== null ? ' '.$mon.number_format($montoACobrar,2) : '' }}
    </a>
@endif

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty"><i class="fa-regular fa-calendar"></i><p>No hay citas para hoy.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection