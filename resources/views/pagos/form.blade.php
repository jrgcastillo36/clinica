@extends('layouts.app')
@section('title', $pago->exists ? 'Editar pago' : 'Registrar pago')

@section('content')
    <div class="page-head">
        <div><h1>{{ $pago->exists ? 'Editar pago' : 'Registrar pago' }}</h1><p>Registro de cobro.</p></div>
        <a href="{{ route('pagos.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $pago->exists ? route('pagos.update',$pago) : route('pagos.store') }}" class="card">
        @csrf
        @if($pago->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field" style="position:relative">
                <label>Paciente *</label>
                @php($pacientePre = $pacientes->firstWhere('id', $pacienteSel))
                <input type="text" id="pacienteBuscar" autocomplete="off"
                    placeholder="Busca por nombre o DNI..."
                    value="{{ old('paciente_nombre', $pacientePre ? $pacientePre->nombre_completo.' — '.$pacientePre->documento : '') }}">
                <input type="hidden" name="paciente_id" id="pacienteId" value="{{ old('paciente_id', $pacienteSel) }}">
                <div id="pacienteLista" class="paciente-lista"></div>
                @error('paciente_id')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field"><label>Servicio (autocompleta)</label>
                <select id="servicioSel" onchange="aplicarServicio()">
                    <option value="">— Manual —</option>
                    @foreach(($servicios ?? []) as $sv)
                        <option value="{{ $sv->nombre }}" data-precio="{{ $sv->precio }}">{{ $sv->nombre }} · {{ auth()->user()->empresa->moneda ?? 'S/' }} {{ number_format($sv->precio,2) }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Concepto *</label><input name="concepto" value="{{ old('concepto',$pago->concepto) }}" placeholder="Consulta, tratamiento..." required></div>
            <div class="field"><label>Monto *</label><input type="number" step="0.01" name="monto" value="{{ old('monto',$pago->monto) }}" required></div>
            <div class="field"><label>Método</label>
                <select name="metodo">
                    @foreach(['efectivo'=>'Efectivo','tarjeta'=>'Tarjeta','transferencia'=>'Transferencia','yape_plin'=>'Yape / Plin','otro'=>'Otro'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('metodo',$pago->metodo)==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Estado</label>
                <select name="estado">
                    @foreach(['pagado'=>'Pagado','pendiente'=>'Pendiente','anulado'=>'Anulado'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('estado',$pago->estado ?? 'pagado')==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ old('fecha', optional($pago->fecha)->format('Y-m-d') ?? now()->toDateString()) }}" required></div>
            <div class="field full"><label>Notas</label><textarea name="notas">{{ old('notas',$pago->notas) }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar pago</button></div>
    </form>

    <style>
    .paciente-lista{display:none;position:absolute;z-index:20;background:#fff;border:1px solid var(--line);
        border-radius:10px;margin-top:4px;max-height:240px;overflow-y:auto;width:100%;
        box-shadow:0 8px 20px rgba(0,0,0,.1)}
    .paciente-lista div{padding:9px 14px;cursor:pointer;font-size:13.5px}
    .paciente-lista div:hover{background:var(--bg-pink)}
    .paciente-lista .doc{color:var(--ink-soft);font-size:12px}
    </style>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Preparar los datos de pacientes para JavaScript
        const pacientes = {!! json_encode($pacientes->map(function($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre_completo,
                'documento' => $p->documento,
            ];
        })->values()->toArray()) !!};

        const input = document.getElementById('pacienteBuscar');
        const hidden = document.getElementById('pacienteId');
        const lista = document.getElementById('pacienteLista');

        function render(items) {
            lista.innerHTML = '';
            if (!items.length) { 
                lista.style.display = 'none'; 
                return; 
            }
            items.slice(0, 30).forEach(function (p) {
                const row = document.createElement('div');
                row.innerHTML = p.nombre + ' <span class="doc">' + (p.documento || '') + '</span>';
                row.addEventListener('click', function () {
                    input.value = p.nombre + ' — ' + (p.documento || '');
                    hidden.value = p.id;
                    lista.style.display = 'none';
                });
                lista.appendChild(row);
            });
            lista.style.display = 'block';
        }

        input.addEventListener('input', function () {
            hidden.value = '';
            const q = input.value.trim().toLowerCase();
            if (!q) { 
                lista.style.display = 'none'; 
                return; 
            }
            render(pacientes.filter(function (p) {
                return p.nombre.toLowerCase().includes(q) || (p.documento || '').toLowerCase().includes(q);
            }));
        });

        input.addEventListener('focus', function () {
            if (input.value.trim()) {
                input.dispatchEvent(new Event('input'));
            }
        });

        document.addEventListener('click', function (e) {
            if (!lista.contains(e.target) && e.target !== input) {
                lista.style.display = 'none';
            }
        });
    });

    function aplicarServicio(){
        const o = document.getElementById('servicioSel');
        const opt = o.options[o.selectedIndex];
        if (!opt.value) return;
        document.querySelector('[name=concepto]').value = opt.value;
        document.querySelector('[name=monto]').value = opt.dataset.precio;
    }
    </script>
    @endpush

@endsection