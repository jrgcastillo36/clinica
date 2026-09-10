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
            <div class="field full" id="pendientesWrap" style="display:none">
                <label>📋 Consultas pendientes de cobro de este paciente</label>
                <select id="pendienteSel" onchange="aplicarPendiente()">
                    <option value="">— Ninguna / cobro manual —</option>
                </select>
            </div>
            <input type="hidden" name="consulta_id" id="consultaId" value="{{ old('consulta_id',$pago->consulta_id) }}">
            <input type="hidden" name="cita_id" value="{{ old('cita_id', $citaSel ?? $pago->cita_id) }}">
            <div class="field" style="position:relative">
                <label>Servicio (autocompleta)</label>
                <input type="text" id="servicioBuscar" autocomplete="off" placeholder="Buscar por código o nombre...">
<input type="hidden" id="servicioSel" name="servicio_elegido_id">
                <div id="servicioLista" class="paciente-lista"></div>
                <small id="categoriaFiltroInfo" style="display:none;">
                    Filtrado por categoría: <strong id="categoriaFiltroNombre"></strong>
                    — <a href="#" onclick="limpiarFiltroCategoria(); return false;">quitar filtro</a>
                </small>
            </div>
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
            <div class="field"><label>N° de cuota (opcional)</label><input type="number" min="1" name="cuota_numero" value="{{ old('cuota_numero',$pago->cuota_numero) }}" placeholder="Ej. 3"></div>
            <div class="field"><label>Total de cuotas (opcional)</label><input type="number" min="1" name="cuota_total" value="{{ old('cuota_total',$pago->cuota_total) }}" placeholder="Ej. 10"></div>
            <div class="field full"><label>Notas</label><textarea name="notas">{{ old('notas',$pago->notas) }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar pago</button></div>
    </form>

 <style>
.paciente-lista{display:none;position:absolute;top:100%;left:0;z-index:20;background:#fff;border:1px solid var(--line);
    border-radius:10px;margin-top:4px;max-height:240px;overflow-y:auto;width:100%;
    box-shadow:0 8px 20px rgba(0,0,0,.1)}
.paciente-lista div{padding:9px 14px;cursor:pointer;font-size:13.5px}
.paciente-lista div:hover{background:var(--bg-pink)}
.paciente-lista .doc{color:var(--ink-soft);font-size:12px}
</style>

    @push('scripts')
   <script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Consultas pendientes de cobro (servicio_fijo + categoria + pago_directo) =====
    const consultasPendientes = {!! json_encode($consultasPendientes->map(function($c) {
        if ($c->tipoPendiente === 'categoria') {
            return [
                'id' => $c->id,
                'paciente_id' => $c->paciente_id,
                'tipoPendiente' => 'categoria',
                'categoria' => $c->categoria_servicio,
                'fecha' => optional($c->fecha)->format('d/m/Y'),
            ];
        }
        if ($c->tipoPendiente === 'pago_directo') {
            return [
                'id' => $c->id,
                'paciente_id' => $c->paciente_id,
                'tipoPendiente' => 'pago_directo',
                'servicio' => $c->servicio->nombre ?? 'Servicio',
                'servicio_id' => $c->servicio_id,
                'saldo' => $c->saldo,
                'pagado' => $c->pagado,
                'total' => $c->total,
                'fecha' => optional($c->fecha)->format('d/m/Y'),
            ];
        }
        $pagado = $c->pago->sum('monto');
        $precio = (float) ($c->servicio->precio ?? 0);
        $saldo = max($precio - $pagado, 0);
        return [
            'id' => $c->id,
            'paciente_id' => $c->paciente_id,
            'tipoPendiente' => 'servicio_fijo',
            'servicio' => $c->servicio->nombre ?? 'Servicio',
            'saldo' => $saldo,
            'pagado' => $pagado,
            'total' => $precio,
            'fecha' => optional($c->fecha)->format('d/m/Y'),
        ];
    })->values()->toArray()) !!};

    function mostrarPendientesDe(pacienteId){
        const wrap = document.getElementById('pendientesWrap');
        const sel = document.getElementById('pendienteSel');
        const encontradas = consultasPendientes.filter(c => c.paciente_id == pacienteId);
        sel.innerHTML = '<option value="">— Ninguna / cobro manual —</option>';
        if (!encontradas.length) { wrap.style.display = 'none'; return; }
        encontradas.forEach(function (c) {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.dataset.tipo = c.tipoPendiente;
            if (c.tipoPendiente === 'categoria') {
                opt.dataset.categoria = c.categoria;
                opt.textContent = c.fecha + ' — Categoría: ' + c.categoria + ' (pendiente de cobro)';
            } else if (c.tipoPendiente === 'pago_directo') {
                opt.dataset.servicio = c.servicio;
                opt.dataset.servicioId = c.servicio_id;
                opt.dataset.precio = c.saldo;
                const detalle = ' (ya pagó S/' + Number(c.pagado).toFixed(2) + ' de S/' + Number(c.total).toFixed(2) + ')';
                opt.textContent = c.fecha + ' — ' + c.servicio + ' [sin cita] — Saldo: S/ ' + Number(c.saldo).toFixed(2) + detalle;
            } else {
                opt.dataset.servicio = c.servicio;
                opt.dataset.precio = c.saldo;
                const detalle = c.pagado > 0 ? ' (pagado S/' + Number(c.pagado).toFixed(2) + ' de S/' + Number(c.total).toFixed(2) + ')' : '';
                opt.textContent = c.fecha + ' — ' + c.servicio + ' — Saldo: S/ ' + Number(c.saldo).toFixed(2) + detalle;
            }
            sel.appendChild(opt);
        });
        wrap.style.display = 'block';
    }

    window.aplicarPendiente = function(){
        const sel = document.getElementById('pendienteSel');
        const opt = sel.options[sel.selectedIndex];

        if (!sel.value) {
            document.getElementById('consultaId').value = '';
            limpiarFiltroCategoria();
            return;
        }

        if (opt.dataset.tipo === 'categoria') {
            document.getElementById('consultaId').value = sel.value;
            document.querySelector('[name=concepto]').value = '';
            document.querySelector('[name=monto]').value = '';
            filtrarServiciosPorCategoria(opt.dataset.categoria);
        } else if (opt.dataset.tipo === 'pago_directo') {
            // No hay consulta que vincular: se deja vacío a propósito.
            document.getElementById('consultaId').value = '';
            document.querySelector('[name=concepto]').value = opt.dataset.servicio;
            document.querySelector('[name=monto]').value = opt.dataset.precio;
            servicioSelHidden.value = opt.dataset.servicioId;
            servicioBuscar.value = opt.dataset.servicio;
            limpiarFiltroCategoria();
        } else {
            document.getElementById('consultaId').value = sel.value;
            document.querySelector('[name=concepto]').value = opt.dataset.servicio;
            document.querySelector('[name=monto]').value = opt.dataset.precio;
            limpiarFiltroCategoria();
        }
    };

    // ===== Buscador de pacientes =====
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
                mostrarPendientesDe(p.id);
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
    if (hidden.value) mostrarPendientesDe(hidden.value);
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

    // ===== Buscador de servicios (reemplaza el <select> de 83 códigos) =====
    const moneda = {!! json_encode(auth()->user()->empresa->moneda ?? 'S/') !!};

    const servicios = {!! json_encode(collect($servicios ?? [])->map(function($s) {
        return [
            'id' => $s->id,
            'codigo' => $s->codigo,
            'nombre' => $s->nombre,
            'categoria' => $s->categoria ?: 'Otros',
            'precio' => $s->precio,
        ];
    })->values()->toArray()) !!};

    let categoriaFiltro = null;
    const servicioBuscar = document.getElementById('servicioBuscar');
    const servicioListaEl = document.getElementById('servicioLista');
    const servicioSelHidden = document.getElementById('servicioSel');

    function serviciosFiltrados(q) {
        let base = servicios;
        if (categoriaFiltro) {
            base = base.filter(function (s) { return s.categoria === categoriaFiltro; });
        }
        if (!q) return base;
        return base.filter(function (s) {
            return s.nombre.toLowerCase().includes(q) || (s.codigo || '').toLowerCase().includes(q);
        });
    }

    function renderServicios(items) {
        servicioListaEl.innerHTML = '';
        if (!items.length) { servicioListaEl.style.display = 'none'; return; }
        items.slice(0, 40).forEach(function (s) {
            const row = document.createElement('div');
            row.innerHTML = (s.codigo ? s.codigo + ' — ' : '') + s.nombre +
                ' <span class="doc">' + s.categoria + ' · ' + moneda + ' ' + Number(s.precio).toFixed(2) + '</span>';
            row.addEventListener('click', function () {
                servicioBuscar.value = (s.codigo ? s.codigo + ' — ' : '') + s.nombre;
                servicioSelHidden.value = s.id;
                document.querySelector('[name=concepto]').value = s.nombre;
                document.querySelector('[name=monto]').value = s.precio;
                servicioListaEl.style.display = 'none';
            });
            servicioListaEl.appendChild(row);
        });
        servicioListaEl.style.display = 'block';
    }

    servicioBuscar.addEventListener('input', function () {
        servicioSelHidden.value = '';
        renderServicios(serviciosFiltrados(servicioBuscar.value.trim().toLowerCase()));
    });

    servicioBuscar.addEventListener('focus', function () {
        servicioBuscar.select();
        renderServicios(serviciosFiltrados(''));
    });

    servicioBuscar.addEventListener('click', function () {
        renderServicios(serviciosFiltrados(''));
    });

    document.addEventListener('click', function (e) {
        if (!servicioListaEl.contains(e.target) && e.target !== servicioBuscar) {
            servicioListaEl.style.display = 'none';
        }
    });

    window.filtrarServiciosPorCategoria = function(categoria){
        categoriaFiltro = categoria;
        servicioBuscar.value = '';
        servicioSelHidden.value = '';
        document.querySelector('[name=concepto]').value = '';
        document.querySelector('[name=monto]').value = '';
        document.getElementById('categoriaFiltroNombre').textContent = categoria;
        document.getElementById('categoriaFiltroInfo').style.display = 'block';
        servicioBuscar.placeholder = 'Buscar código en "' + categoria + '"...';
        servicioBuscar.focus();
        renderServicios(serviciosFiltrados(''));
    };

    window.limpiarFiltroCategoria = function(){
        categoriaFiltro = null;
        document.getElementById('categoriaFiltroInfo').style.display = 'none';
        servicioBuscar.placeholder = 'Buscar por código o nombre...';
    };
});
</script>
    @endpush

@endsection