@extends('layouts.app')
@section('title', 'Servicios')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head"><div><h1>Servicios y precios</h1><p>Catálogo que autocompleta el cobro al registrar un pago.</p></div></div>

    <div class="card mb">
        <h3 class="mb" id="tituloFormServicio">Nuevo servicio</h3>
        <form method="POST" action="{{ route('admin.servicios.store') }}" id="formServicio">
            @csrf
            <input type="hidden" name="_method" id="metodoServicio" value="POST">
            <div class="form-grid">
                <div class="field mb">
                    <label>Categoría</label>
                    <select id="fs_categoria_select" onchange="onCategoriaChange()">
                        <option value="">— Selecciona una categoría —</option>
                        @foreach($servicios->keys() as $cat)
                            @if($cat !== 'Sin categoría')<option value="{{ $cat }}">{{ $cat }}</option>@endif
                        @endforeach
                        <option value="__nueva__">➕ Nueva categoría...</option>
                    </select>
                    <input type="text" name="categoria" id="fs_categoria" placeholder="Escribe el nombre de la nueva categoría" style="display:none;margin-top:8px">
                </div>
                <div class="field mb"><label>Código</label><input name="codigo" id="fs_codigo" placeholder="Ej. S001"></div>
                <div class="field mb"><label>Descripción *</label><input name="nombre" id="fs_nombre" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field mb"><label>Precio *</label><input type="number" step="0.01" name="precio" id="fs_precio" required></div>
                <div class="field mb"><label>Especialidad</label>
                    <select name="especialidad_id" id="fs_especialidad">
                        <option value="">General</option>
                        @foreach($especialidades as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach
                    </select></div>
            </div>
            <div class="flex gap">
                <button type="submit" class="btn btn-primary" id="btnGuardarServicio"><i class="fa-solid fa-plus"></i> Agregar</button>
                <button type="button" class="btn btn-ghost" id="btnCancelarEdicion" style="display:none" onclick="cancelarEdicionServicio()">Cancelar</button>
            </div>
        </form>
    </div>

    @forelse($servicios as $categoria => $items)
        <details class="table-wrap mb" style="padding:0" {{ $loop->first ? 'open' : '' }}>
<summary style="cursor:pointer;background:var(--navy);color:#fff;padding:10px 14px;font-size:12.5px;font-weight:700;display:flex;justify-content:space-between;align-items:center">
                <span>{{ $categoria }}</span>
                <span style="font-size:11px;opacity:.9;font-weight:500">{{ $items->count() }} servicio(s)</span>            </summary>
            <table>
                <thead>
                    <tr><th style="width:70px">Código</th><th>Descripción</th><th>Especialidad</th><th>Precio</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($items as $s)
                    <tr>
                        <td class="muted">{{ $s->codigo ?? '—' }}</td>
                        <td><b>{{ $s->nombre }}</b></td>
                        <td>{{ $s->especialidad->nombre ?? 'General' }}</td>
                        <td><b>@money($s->precio, null, 2)</b></td>
                        <td style="text-align:right;white-space:nowrap">
                            <button type="button" class="btn btn-light btn-sm"
                                data-id="{{ $s->id }}"
                                data-categoria="{{ $s->categoria }}"
                                data-codigo="{{ $s->codigo }}"
                                data-nombre="{{ $s->nombre }}"
                                data-precio="{{ $s->precio }}"
                                data-especialidad="{{ $s->especialidad_id }}"
                                data-url="{{ route('admin.servicios.update', $s) }}"
                                onclick="editarServicio(this)"><i class="fa-solid fa-pen"></i></button>
                            <form method="POST" action="{{ route('admin.servicios.destroy',$s) }}" style="display:inline" onsubmit="return confirm('¿Eliminar servicio?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </details>
    @empty
        <div class="table-wrap"><div class="empty"><i class="fa-solid fa-tags"></i><p>Sin servicios. Agrega el primero.</p></div></div>
    @endforelse

    <style>
    details.table-wrap summary{list-style:none}
    details.table-wrap summary::-webkit-details-marker{display:none}
    details.table-wrap summary::after{content:"\f078";font-family:"Font Awesome 6 Free";font-weight:900;margin-left:10px}
    details.table-wrap[open] summary::after{content:"\f077"}
    </style>

    @push('scripts')
    <script>
    function onCategoriaChange() {
        const sel = document.getElementById('fs_categoria_select');
        const txt = document.getElementById('fs_categoria');
        if (sel.value === '__nueva__') {
            txt.style.display = 'block';
            txt.value = '';
            txt.focus();
        } else {
            txt.style.display = 'none';
            txt.value = sel.value;
        }
    }

    function editarServicio(btn) {
        document.getElementById('formServicio').action = btn.dataset.url;
        document.getElementById('metodoServicio').value = 'PUT';

        const cat = btn.dataset.categoria || '';
        const sel = document.getElementById('fs_categoria_select');
        const txt = document.getElementById('fs_categoria');
        let existe = false;
        for (const opt of sel.options) { if (opt.value === cat) { existe = true; break; } }
        if (existe && cat) {
            sel.value = cat;
            txt.style.display = 'none';
            txt.value = cat;
        } else {
            sel.value = '__nueva__';
            txt.style.display = 'block';
            txt.value = cat;
        }

        document.getElementById('fs_codigo').value = btn.dataset.codigo || '';
        document.getElementById('fs_nombre').value = btn.dataset.nombre || '';
        document.getElementById('fs_precio').value = btn.dataset.precio || '';
        document.getElementById('fs_especialidad').value = btn.dataset.especialidad || '';
        document.getElementById('tituloFormServicio').textContent = 'Editar servicio';
        document.getElementById('btnGuardarServicio').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Actualizar';
        document.getElementById('btnCancelarEdicion').style.display = 'inline-flex';
        document.getElementById('formServicio').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function cancelarEdicionServicio() {
        const form = document.getElementById('formServicio');
        form.action = '{{ route('admin.servicios.store') }}';
        document.getElementById('metodoServicio').value = 'POST';
        form.reset();
        document.getElementById('fs_categoria_select').value = '';
        document.getElementById('fs_categoria').style.display = 'none';
        document.getElementById('tituloFormServicio').textContent = 'Nuevo servicio';
        document.getElementById('btnGuardarServicio').innerHTML = '<i class="fa-solid fa-plus"></i> Agregar';
        document.getElementById('btnCancelarEdicion').style.display = 'none';
    }
    </script>
    @endpush
@endsection