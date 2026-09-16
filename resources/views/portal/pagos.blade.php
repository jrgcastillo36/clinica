@extends('portal.layout')
@section('title','Mis pagos')
@section('content')
    @php $mon = $empresa->moneda ?? 'S/'; @endphp
        <h1 style="margin:0 0 16px">Mis pagos</h1>

    <div class="grid g-2" style="grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
        <div class="card" style="background:#dcfce7">
            <div class="muted" style="font-size:12px;text-transform:uppercase;font-weight:600">Total pagado</div>
            <div style="font-size:26px;font-weight:700;color:#166534">{{ $mon }} {{ number_format($totalPagado, 2) }}</div>
        </div>
        <div class="card" style="background:{{ $totalPendiente > 0 ? '#fef3c7' : '#f1f5f9' }}">
            <div class="muted" style="font-size:12px;text-transform:uppercase;font-weight:600">Saldo pendiente</div>
            <div style="font-size:26px;font-weight:700;color:{{ $totalPendiente > 0 ? '#92400e' : '#475569' }}">{{ $mon }} {{ number_format($totalPendiente, 2) }}</div>
        </div>
    </div>

       <div class="table-wrap">
        <table>
            <thead><tr><th>Fecha</th><th>Concepto</th><th>Método</th><th>Monto</th><th>Estado</th></tr></thead>
            <tbody>
            @forelse($pagos as $p)
                <tr>
                    <td>{{ $p->fecha->format('d/m/Y') }}</td>
                    <td>{{ $p->concepto }}</td>
                    <td>{{ $p->metodo_label }}</td>
                    <td><b>@money($p->monto, null, 2)</b></td>
                    <td>@php $mp=['pagado'=>'green','pendiente'=>'amber','anulado'=>'red'][$p->estado]??'gray'; @endphp<span class="pill {{ $mp }}">{{ ucfirst($p->estado) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-receipt"></i><p>No tienes pagos registrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- ==========================================
     MEDIOS DE PAGO
=========================================== -->
<div class="card mb" style="
    margin-top:20px;
    padding:0;
    overflow:hidden;
    border-radius:14px;
    border:1px solid #e5e7eb;
    box-shadow:0 4px 14px rgba(0,0,0,.06);
">

    <!-- TÍTULO -->
    <div style="
        padding:16px 20px;
        background:#f8fafc;
        border-bottom:1px solid #e5e7eb;
        display:flex;
        align-items:center;
        gap:10px;
    ">
        <i class="fa-solid fa-credit-card" style="
            color:#0d9488;
            font-size:20px;
        "></i>

        <h3 style="
            margin:0;
            font-size:18px;
            font-weight:700;
            color:#1f2937;
        ">
            Medios de pago
        </h3>
    </div>

    <!-- BANNER DE PAGO -->
    @if($empresa->banner_pago)
        <div style="
            text-align:center;
            padding:18px;
            background:#fff;
        ">
            <img
                src="{{ asset('storage/'.$empresa->banner_pago) }}"
                alt="Medios de pago"
                style="
                    display:block;
                    max-width:100%;
                    max-height:auto;
                    width:auto;
                    height:auto;
                    margin:0 auto;
                    border-radius:10px;
                "
            >
        </div>
    @endif

    <!-- INFORMACIÓN ADICIONAL -->
    @if($empresa->info_pago)
        <div style="
            margin:0 18px 18px;
            padding:14px 16px;
            background:#f0fdf9;
            border:1px solid #99f6e4;
            border-radius:10px;
        ">

            <h4 style="
                margin:0 0 6px;
                font-size:15px;
                font-weight:700;
                color:#0f766e;
            ">
                <i class="fa-solid fa-money-bill-wave"
                   style="margin-right:6px;">
                </i>
                ¿Cómo puedo pagar?
            </h4>

            <p style="
                margin:0;
                color:#475569;
                font-size:13px;
            ">
                También puedes pagar en efectivo directamente en recepción.
            </p>

            <p style="
                white-space:pre-line;
                font-size:13.5px;
                margin:8px 0 0;
                color:#334155;
            ">
                {{ $empresa->info_pago }}
            </p>

        </div>
    @endif

</div>

@endsection