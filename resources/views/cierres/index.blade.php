@extends('layouts.app')
@section('title', 'Cierre de caja')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head">
        <div><h1>Cierre de caja</h1><p>Manejo por turnos — {{ now()->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</p></div>
    </div>

    @if(!$cierre)
        {{-- No hay turno abierto ahora mismo: mostrar formulario para abrir uno --}}
        <div class="card" style="max-width:460px">
            <h3 class="mb"><i class="fa-solid fa-cash-register" style="color:var(--violet-2)"></i> Abrir turno</h3>
            <p class="muted" style="font-size:12.5px;margin-bottom:14px">Escribe cuánto efectivo hay físicamente en caja para empezar el turno.</p>
            <form method="POST" action="{{ route('cierres.abrir') }}">
                @csrf
                <div class="field mb">
                    <label>Nombre del turno (opcional)</label>
                    <input type="text" name="turno_nombre" placeholder="Ej. Mañana, Turno Ana..." value="{{ old('turno_nombre') }}">
                </div>
                <div class="field mb">
                    <label>Efectivo inicial *</label>
                    <input type="number" step="0.01" min="0" name="efectivo_inicial" value="{{ old('efectivo_inicial', number_format($fondoSugerido,2,'.','')) }}" required>
                    @if($fondoSugerido > 0)
                        <small class="muted" style="font-size:11.5px">Sugerido: {{ $mon }} {{ number_format($fondoSugerido,2) }} (lo contado en el último turno cerrado).</small>
                    @endif
                    @error('efectivo_inicial')<span class="err">{{ $message }}</span>@enderror
                </div>
                <div class="field mb">
                    <label>Motivo del ajuste (solo si cambiaste el monto sugerido)</label>
                    <textarea name="motivo_ajuste_fondo" placeholder="Ej. Se retiraron S/50 para depósito bancario.">{{ old('motivo_ajuste_fondo') }}</textarea>
                    @error('motivo_ajuste_fondo')<span class="err">{{ $message }}</span>@enderror
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-door-open"></i> Abrir turno</button>
            </form>
        </div>

    @else
        {{-- Hay un turno abierto --}}
        <div class="card mb" style="background:#dbeafe">
            <div class="flex gap" style="align-items:center">
                <i class="fa-solid fa-door-open" style="font-size:20px;color:#1e40af"></i>
                <div>
                    <b style="color:#1e40af">Turno abierto{{ $cierre->turno_nombre ? ' — '.$cierre->turno_nombre : '' }}</b>
                    <p class="muted" style="margin:2px 0 0">
                        Por {{ $cierre->abiertoPor->name ?? '—' }} desde las {{ $cierre->created_at->format('H:i') }}
                        · Fondo inicial: {{ $mon }} {{ number_format($cierre->efectivo_inicial,2) }}
                    </p>
                    @if($cierre->motivo_ajuste_fondo)
                        <p class="muted" style="margin:2px 0 0;font-size:11.5px">📝 Ajuste de fondo: {{ $cierre->motivo_ajuste_fondo }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid g-2" style="grid-template-columns:1fr 1fr">
            <div class="card">
                <h3 class="mb">Resumen del sistema (este turno)</h3>
                <div class="table-wrap" style="box-shadow:none">
                    <table>
                        <thead><tr><th>Método</th><th>Total</th></tr></thead>
                        <tbody>
                        @forelse($porMetodo as $metodo => $monto)
                            <tr><td>{{ ucfirst(str_replace('_',' ',$metodo)) }}</td><td><b>{{ $mon }} {{ number_format($monto,2) }}</b></td></tr>
                        @empty
                            <tr><td colspan="2"><div class="empty"><i class="fa-solid fa-cash-register"></i><p>Sin pagos registrados en este turno todavía.</p></div></td></tr>
                        @endforelse
                        </tbody>
                        <tfoot>
                            <tr style="font-weight:700;background:var(--bg-pink)"><td>Total general</td><td>{{ $mon }} {{ number_format($totalSistema,2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>
               <div class="grid g-2 mt" style="grid-template-columns:1fr 1fr;gap:10px">
    <div class="metric">
        <div class="big">{{ $mon }} {{ number_format($cierre->efectivo_inicial + $efectivoSistema,2) }}</div>
        <div class="cap">Efectivo esperado (solo billetes/monedas)</div>
    </div>
    <div class="metric">
        <div class="big">{{ $mon }} {{ number_format($cierre->efectivo_inicial + $totalSistema,2) }}</div>
        <div class="cap">Total esperado (todos los medios de pago)</div>
    </div>
</div>
            </div>

            <div class="card">
                <h3 class="mb">Cerrar el turno</h3>
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
                    <button class="btn btn-primary" onclick="return confirm('¿Cerrar este turno? Podrás reabrirlo después si es necesario.')"><i class="fa-solid fa-lock"></i> Cerrar turno</button>
                </form>
            </div>
        </div>
    @endif

    @error('reabrir')
        <div class="alert" style="background:#fee2e2;color:#991b1b;margin-top:14px"><i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}</div>
    @enderror

    <h3 class="mt mb">Historial de turnos</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Turno</th><th>Apertura</th><th>Cierre</th><th>Fondo inicial</th><th>Total del turno</th><th>Esperado</th><th>Contado</th><th>Diferencia</th><th></th></tr></thead>
            <tbody>
            @forelse($historial as $c)
                <tr>
                    <td>{{ $c->turno_nombre ?: '—' }}<br><span class="muted" style="font-size:11px">{{ $c->fecha->format('d/m/Y') }}</span></td>
                    <td>{{ $c->created_at->format('d/m H:i') }}</td>
                    <td>{{ $c->cerrado_at->format('d/m H:i') }}</td>
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
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('cierres.pdf', $c) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa-solid fa-file-pdf"></i></a>
                        <form method="POST" action="{{ route('cierres.reabrir', $c) }}" style="display:inline" onsubmit="return confirm('¿Reabrir este turno? Podrás modificar su cuadre y deberás volver a cerrarlo.')">
                            @csrf
                            <button class="btn btn-light btn-sm" style="color:#b45309"><i class="fa-solid fa-unlock"></i> Reabrir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="empty"><i class="fa-solid fa-clock-rotate-left"></i><p>Todavía no hay turnos cerrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection