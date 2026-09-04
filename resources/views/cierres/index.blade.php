@extends('layouts.app')
@section('title', 'Cierre de caja')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Cierre de caja</h1><p>Corte del día de hoy — {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</p></div>
    </div>

    @if(!$cierre)
        {{-- ESTADO 1: la caja de hoy todavía no se abre --}}
        <div class="card" style="max-width:420px">
            <h3 class="mb"><i class="fa-solid fa-cash-register" style="color:var(--violet-2)"></i> Abrir caja</h3>
            <p class="muted" style="font-size:12.5px;margin-bottom:14px">Escribe cuánto efectivo hay en caja para empezar el día (para dar vueltos). Si no usan fondo, deja 0.</p>
            <form method="POST" action="{{ route('cierres.abrir') }}">
                @csrf
                <div class="field mb">
                    <label>Efectivo inicial *</label>
                    <input type="number" step="0.01" min="0" name="efectivo_inicial" value="0" required>
                    @error('efectivo_inicial')<span class="err">{{ $message }}</span>@enderror
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-door-open"></i> Abrir caja</button>
            </form>
        </div>

    @elseif(!$cierre->estaCerrado())
        {{-- ESTADO 2: caja abierta, esperando el cierre --}}
        <div class="card mb" style="background:#dbeafe">
            <div class="flex gap" style="align-items:center">
                <i class="fa-solid fa-door-open" style="font-size:20px;color:#1e40af"></i>
                <div>
                    <b style="color:#1e40af">Caja abierta</b>
                    <p class="muted" style="margin:2px 0 0">Por {{ $cierre->abiertoPor->name ?? '—' }} · Fondo inicial: {{ $mon }} {{ number_format($cierre->efectivo_inicial,2) }}</p>
                </div>
            </div>
        </div>

        <div class="grid g-2" style="grid-template-columns:1fr 1fr">
            <div class="card">
                <h3 class="mb">Resumen del sistema (hoy)</h3>
                <div class="table-wrap" style="box-shadow:none">
                    <table>
                        <thead><tr><th>Método</th><th>Total</th></tr></thead>
                        <tbody>
                        @forelse($porMetodo as $metodo => $monto)
                            <tr><td>{{ ucfirst(str_replace('_',' ',$metodo)) }}</td><td><b>{{ $mon }} {{ number_format($monto,2) }}</b></td></tr>
                        @empty
                            <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-cash-register"></i><p>Sin pagos registrados hoy todavía.</p></div></td></tr>
                        @endforelse
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:700;background:var(--bg-pink)"><td>Total general</td><td>{{ $mon }} {{ number_format($totalSistema,2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>
                <div class="metric mt">
                    <div class="big">{{ $mon }} {{ number_format($cierre->efectivo_inicial + $efectivoSistema,2) }}</div>
                    <div class="cap">Efectivo esperado (fondo + cobrado hoy)</div>
                </div>
            </div>

            <div class="card">
                <h3 class="mb">Cerrar el día</h3>
                <form method="POST" action="{{ route('cierres.cerrar') }}">
                    @csrf
                    <div class="field mb">
                        <label>Efectivo contado físicamente *</label>
                        <input type="number" step="0.01" min="0" name="efectivo_contado" placeholder="0.00" required>
                        @error('efectivo_contado')<span class="err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field mb">
                        <label>Observaciones (opcional)</label>
                        <textarea name="observaciones" placeholder="Ej. Faltó registrar un pago de S/20, se corrigió al día siguiente."></textarea>
                    </div>
                    <button class="btn btn-primary" onclick="return confirm('¿Cerrar el día? Ya no podrás modificar este cierre.')"><i class="fa-solid fa-lock"></i> Cerrar el día</button>
                </form>
            </div>
        </div>

    @else
        {{-- ESTADO 3: día ya cerrado --}}
        <div class="card mb" style="background:#dcfce7">
            <div class="flex gap" style="align-items:center">
                <i class="fa-solid fa-circle-check" style="font-size:22px;color:#166534"></i>
                <div>
                    <b style="color:#166534">El día de hoy ya fue cerrado</b>
                    <p class="muted" style="margin:2px 0 0">Abierta por {{ $cierre->abiertoPor->name ?? '—' }} · Cerrada por {{ $cierre->usuario->name ?? '—' }} a las {{ $cierre->cerrado_at->format('H:i') }}</p>
                </div>
                <a href="{{ route('cierres.pdf', $cierre) }}" target="_blank" class="btn btn-light" style="margin-left:auto"><i class="fa-solid fa-file-pdf"></i> Ver PDF</a>
            </div>
            <div class="grid g-4 mt">
                <div class="metric"><div class="big">{{ $mon }} {{ number_format($cierre->efectivo_inicial,2) }}</div><div class="cap">Fondo inicial</div></div>
                <div class="metric"><div class="big">{{ $mon }} {{ number_format($cierre->total_sistema,2) }}</div><div class="cap">Total cobrado</div></div>
                <div class="metric"><div class="big">{{ $mon }} {{ number_format($cierre->efectivo_contado,2) }}</div><div class="cap">Efectivo contado</div></div>
                <div class="metric" style="{{ $cierre->diferencia != 0 ? 'border-color:#f59e0b' : '' }}">
                    <div class="big" style="{{ $cierre->diferencia != 0 ? 'color:#b45309;background:none;-webkit-text-fill-color:#b45309' : '' }}">{{ $mon }} {{ number_format($cierre->diferencia,2) }}</div>
                    <div class="cap">Diferencia</div>
                </div>
            </div>
            @if($cierre->observaciones)
                <p class="muted mt" style="font-size:12.5px">📝 {{ $cierre->observaciones }}</p>
            @endif
        </div>
    @endif

    <h3 class="mt mb">Historial de cierres</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Fecha</th><th>Fondo inicial</th><th>Total del día</th><th>Efectivo esperado</th><th>Contado</th><th>Diferencia</th><th></th></tr></thead>
            <tbody>
            @forelse($historial as $c)
                <tr>
                    <td>{{ $c->fecha->format('d/m/Y') }}</td>
                    <td>{{ $mon }} {{ number_format($c->efectivo_inicial,2) }}</td>
                    <td>{{ $mon }} {{ number_format($c->total_sistema,2) }}</td>
                    <td>{{ $mon }} {{ number_format($c->efectivo_esperado,2) }}</td>
                    <td>{{ $mon }} {{ number_format($c->efectivo_contado,2) }}</td>
                    <td>
                        @if($c->diferencia == 0)
                            <span class="pill green">Cuadrado</span>
                        @else
                            <span class="pill amber">{{ $mon }} {{ number_format($c->diferencia,2) }}</span>
                        @endif
                    </td>
                    <td><a href="{{ route('cierres.pdf', $c) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-file-pdf"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-clock-rotate-left"></i><p>Todavía no hay cierres registrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection