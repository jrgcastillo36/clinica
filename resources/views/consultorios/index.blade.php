@extends('layouts.app')
@section('title', 'Consultorios')

@section('content')
    <div class="page-head">
        <div><h1>Consultorios</h1><p>Salas físicas de atención de la clínica.</p></div>
    </div>

    <div class="grid g-2" style="grid-template-columns:1fr 1.4fr">
        <div class="card">
            <h3 class="mb">Nuevo consultorio</h3>
            <form method="POST" action="{{ route('admin.consultorios.store') }}">
                @csrf
                <div class="field mb">
                    <label>Nombre</label>
                    <input name="nombre" placeholder="Ej. Consultorio 1" required>
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Agregar</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead><tr><th>Nombre</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @forelse($consultorios as $c)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('admin.consultorios.update', $c) }}" class="flex gap">
                                @csrf @method('PUT')
                                <input name="nombre" value="{{ $c->nombre }}" style="max-width:200px">
                                <input type="hidden" name="activo" value="0">
                                <label style="display:flex;align-items:center;gap:6px;font-weight:400">
                                    <input type="checkbox" name="activo" value="1" @checked($c->activo)> Activo
                                </label>
                                <button class="btn btn-light btn-sm"><i class="fa-solid fa-floppy-disk"></i></button>
                            </form>
                        </td>
                        <td>
                            @if($c->activo)
                                <span class="pill green">Activo</span>
                            @else
                                <span class="pill gray">Inactivo</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('admin.consultorios.destroy', $c) }}" onsubmit="return confirm('¿Eliminar este consultorio?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3"><div class="empty"><i class="fa-solid fa-door-open"></i><p>No hay consultorios registrados todavía.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
