@extends('layouts.app')
@section('title', 'Estados de cuenta')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Estados de cuenta</h1><p>Pacientes con saldo pendiente.</p></div>
        <a href="{{ route('pagos.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Pagos</a>
    </div>
    <form method="GET" class="card mb" style="padding:14px">
        <div class="flex gap">
            <div class="search" style="max-width:none;flex:1">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar paciente por nombre...">
            </div>
            <button class="btn btn-primary">Buscar</button>
            @if($q)<a href="{{ route('pagos.estados') }}" class="btn btn-ghost">Limpiar</a>@endif
        </div>
    </form>

    <div class="grid g-3 mb">
        <div class="card"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Deudores</div><div style="font-size:26px;font-weight:700;margin-top:6px">{{ $deudores->count() }}</div></div>
        <div class="card pink"><div class="cap" style="font-size:12px;color:var(--ink-soft);text-transform:uppercase">Deuda total</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#b45309">@money($totalDeuda, null, 2)</div></div>
        <div class="card" style="background:#fee2e2"><div class="cap" style="font-size:12px;color:#991b1b;text-transform:uppercase">Vencido (+60 días)</div><div style="font-size:26px;font-weight:700;margin-top:6px;color:#991b1b">@money($totalVencido, null, 2)</div></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Paciente</th><th>Pagos pend.</th><th>Desde</th><th>0-30 días</th><th>31-60 días</th><th>+60 días</th><th>Saldo total</th><th></th></tr></thead>
            <tbody>
            @forelse($deudores as $d)
                @php
                    $tel = preg_replace('/[^0-9]/', '', (string) optional($d->paciente)->telefono);
                    $msg = rawurlencode('Hola '.optional($d->paciente)->nombres.', te recordamos que tienes un saldo pendiente de '.$mon.' '.number_format($d->deuda,2).' en '.(auth()->user()->empresa->nombre ?? 'la clínica').'. ¡Gracias!');
                @endphp
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr(optional($d->paciente)->nombres,0,1) }}{{ mb_substr(optional($d->paciente)->apellidos,0,1) }}</span>{{ optional($d->paciente)->nombre_completo ?? '—' }}</td>
                    <td>{{ $d->items }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($d->desde)->format('d/m/Y') }}</td>
<td>@if($d->dias_0_30 > 0)@money($d->dias_0_30, null, 2)@else—@endif</td>
                    <td>@if($d->dias_31_60 > 0)<span style="color:#b45309">@money($d->dias_31_60, null, 2)</span>@else—@endif</td>
                    <td>@if($d->dias_61_mas > 0)<b style="color:#991b1b">@money($d->dias_61_mas, null, 2)</b>@else—@endif</td>
                    <td><b style="color:#b45309">@money($d->deuda, null, 2)</b></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('pacientes.show', $d->paciente_id) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i></a>
                       <a href="{{ route('pagos.estado-cuenta-paciente', $d->paciente_id) }}" target="_blank" class="btn btn-light btn-sm" title="Estado de cuenta PDF"><i class="fa-solid fa-file-invoice-dollar"></i></a>
                        @if($tel)<a href="https://wa.me/{{ $tel }}?text={{ $msg }}" target="_blank" class="btn btn-light btn-sm" style="color:#25d366"><i class="fa-brands fa-whatsapp"></i></a>@endif
                        <a href="{{ route('pagos.create', ['paciente_id' => $d->paciente_id]) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-money-bill"></i> Cobrar</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty"><i class="fa-solid fa-circle-check"></i><p>No hay saldos pendientes. ¡Todo al día!</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection