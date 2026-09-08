@extends('layouts.app')
@section('title', 'Servicios')

@section('content')
    @php $mon = auth()->user()->empresa->moneda ?? 'S/'; @endphp
    <div class="page-head"><div><h1>Servicios y precios</h1><p>Catálogo que autocompleta el cobro al registrar un pago.</p></div></div>

    <div class="grid g-2">
        <div class="card" style="height:fit-content">
            <h3 class="mb">Nuevo servicio</h3>
            <form method="POST" action="{{ route('admin.servicios.store') }}">
                @csrf
                <div class="field mb"><label>Categoría</label><input name="categoria" placeholder="Ej. Evaluaciones Psicológicas - General" list="listaCategorias">
                    <datalist id="listaCategorias">
                        @foreach($servicios->keys() as $cat)
                            @if($cat !== 'Sin categoría')<option value="{{ $cat }}">@endif
                        @endforeach
                    </datalist>
                </div>
                <div class="field mb"><label>Código</label><input name="codigo" placeholder="Ej. S001" style="max-width:120px"></div>
                <div class="field mb"><label>Descripción *</label><input name="nombre" required>@error('nombre')<span class="err">{{ $message }}</span>@enderror</div>
                <div class="field mb"><label>Precio *</label><input type="number" step="0.01" name="precio" required></div>
                <div class="field mb"><label>Especialidad</label>
                    <select name="especialidad_id">
                        <option value="">General</option>
                        @foreach($especialidades as $e)<option value="{{ $e->id }}">{{ $e->nombre }}</option>@endforeach
                    </select></div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar</button>
            </form>
        </div>

        <div>
            @forelse($servicios as $categoria => $items)
                <div class="table-wrap mb">
                    <table>
                        <thead>
                            <tr><th colspan="5" style="background:var(--violet);color:#fff;font-size:12.5px">{{ $categoria }}</th></tr>
                            <tr><th style="width:70px">Código</th><th>Descripción</th><th>Especialidad</th><th>Precio</th><th></th></tr>
                        </thead>
                        <tbody>
                        @foreach($items as $s)
                            <tr>
                                <td class="muted">{{ $s->codigo ?? '—' }}</td>
                                <td><b>{{ $s->nombre }}</b></td>
                                <td>{{ $s->especialidad->nombre ?? 'General' }}</td>
                                <td><b>@money($s->precio, null, 2)</b></td>
                                <td style="text-align:right">
                                    <form method="POST" action="{{ route('admin.servicios.destroy',$s) }}" onsubmit="return confirm('¿Eliminar servicio?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="table-wrap"><div class="empty"><i class="fa-solid fa-tags"></i><p>Sin servicios. Agrega el primero.</p></div></div>
            @endforelse
        </div>
    </div>
@endsection