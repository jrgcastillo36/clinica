@extends('layouts.app')
@section('title', 'Pacientes')

@section('content')
    <div class="page-head">
        <div><h1>Pacientes</h1><p>Directorio de pacientes de la clínica.</p></div>
       <div class="flex gap">
@unless(auth()->user()->isMedico())
<a href="{{ route('pacientes.exportar', request()->only('q')) }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Exportar</a>
<a href="{{ route('pacientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo paciente</a>
@endunless
</div>
    </div>

    <form method="GET" class="card mb" style="padding:14px">
        <div class="flex gap">
            <div class="search" style="max-width:none;flex:1">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, apellido o documento...">
            </div>
          <select name="orden" onchange="this.form.submit()" style="width:auto">
    <option value="alfabetico" {{ $orden === 'alfabetico' ? 'selected' : '' }}>Alfabético</option>
    <option value="recientes" {{ $orden === 'recientes' ? 'selected' : '' }}>Más recientes primero</option>
    <option value="atendidos" {{ $orden === 'atendidos' ? 'selected' : '' }}>Últimos atendidos primero</option>
</select>
<button class="btn btn-primary">Buscar</button>
@if($q)<a href="{{ route('pacientes.index') }}" class="btn btn-ghost">Limpiar</a>@endif
        </div>
    </form>

    <div class="table-wrap">
        <table>
<thead><tr><th>Paciente</th><th>Documento</th><th>Edad</th><th>Sexo</th><th>Especialidad</th><th>Teléfono</th>@if($orden === 'atendidos')<th>Última atención</th>@endif<th></th></tr></thead>            <tbody>
            @forelse($pacientes as $p)
                <tr>
                    <td><span class="avatar-sm">{{ mb_substr($p->nombres,0,1) }}{{ mb_substr($p->apellidos,0,1) }}</span>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->tipo_documento }} {{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->edad !== null ? $p->edad.' años' : '—' }}</td>
                    <td>{{ ['M'=>'Masculino','F'=>'Femenino','O'=>'Otro'][$p->sexo] ?? '—' }}</td>
                    <td>@if($p->especialidad)<span class="pill pink"><i class="fa-solid {{ $p->especialidad->icono }}"></i> {{ $p->especialidad->nombre }}</span>@else — @endif</td>
<td>{{ $p->telefono ?? '—' }}</td>
@if($orden === 'atendidos')<td>{{ $p->consultas_max_fecha ? \Carbon\Carbon::parse($p->consultas_max_fecha)->format('d/m/Y') : 'Sin consultas' }}</td>@endif
<td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('pacientes.show', $p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-eye"></i></a>
                      
                        @unless(auth()->user()->isMedico())
                        <a href="{{ route('pacientes.edit', $p) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i></a>
                        @endunless
                        @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('pacientes.destroy', $p) }}" style="display:inline" onsubmit="return confirm('¿ELIMINAR PERMANENTEMENTE a este paciente?\n\nSe borrarán también todas sus citas, consultas, pagos y archivos. Esta acción NO se puede deshacer.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty"><i class="fa-solid fa-user-injured"></i><p>No hay pacientes registrados todavía.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $pacientes->links() }}
@endsection
