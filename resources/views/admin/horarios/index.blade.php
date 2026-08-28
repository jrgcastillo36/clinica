@extends('layouts.app')
@section('title', 'Horarios')

@section('content')
    <div class="page-head"><div><h1>Horarios de atención</h1><p>Disponibilidad semanal de cada médico. La reserva online la respeta.</p></div></div>

    @forelse($medicos as $m)
        <div class="card mb">
            <div class="flex between mb">
                <h3 style="margin:0"><span class="avatar-sm">{{ $m->initials() }}</span>{{ $m->titulo_profesional }} {{ $m->name }}</h3>
            </div>

            <div class="flex gap mb" style="flex-wrap:wrap">
                @forelse($m->horarios as $h)
                    <span class="pill violet" style="display:inline-flex;align-items:center;gap:8px">
                        {{ $dias[$h->dia_semana] }} {{ \Illuminate\Support\Str::of($h->hora_inicio)->substr(0,5) }}–{{ \Illuminate\Support\Str::of($h->hora_fin)->substr(0,5) }}
                        <form method="POST" action="{{ route('admin.horarios.destroy',$h) }}" style="display:inline">@csrf @method('DELETE')<button style="border:none;background:none;cursor:pointer;color:#be185d"><i class="fa-solid fa-xmark"></i></button></form>
                    </span>
                @empty
                    <span class="muted">Sin horarios definidos (se usa el horario general de la clínica).</span>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.horarios.store') }}" class="flex gap" style="flex-wrap:wrap;align-items:flex-end">
                @csrf
                <input type="hidden" name="user_id" value="{{ $m->id }}">
                <div class="field"><label>Día</label>
                    <select name="dia_semana">
                        @foreach($dias as $i => $d)<option value="{{ $i }}">{{ $d }}</option>@endforeach
                    </select></div>
                <div class="field"><label>Desde</label><input type="time" name="hora_inicio" value="09:00" required></div>
                <div class="field"><label>Hasta</label><input type="time" name="hora_fin" value="13:00" required></div>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Agregar</button>
            </form>
        </div>
        @empty
        <div class="card"><div class="empty"><i class="fa-solid fa-user-doctor"></i><p>No hay médicos registrados. Créalos en Usuarios.</p></div></div>
    @endforelse

    <div class="card mt">
        <button type="button" class="btn btn-light" onclick="document.getElementById('panelBloqueos').classList.toggle('abierto')" style="margin-bottom:0">
            <i class="fa-solid fa-ban"></i> Más opciones — Bloqueos de horario (vacaciones, capacitaciones) <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div id="panelBloqueos" class="panel-bloqueos">
            <div class="grid g-2 mt" style="grid-template-columns:1fr 1.4fr">
                <div>
                    <h3 class="mb">Nuevo bloqueo</h3>
                    <form method="POST" action="{{ route('bloqueos.store') }}">
                        @csrf
                        <div class="field mb">
                            <label>Médico *</label>
                            <select name="medico_id" required>
                                <option value="">— Selecciona —</option>
                                @foreach($medicos as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @error('medico_id')<span class="err">{{ $message }}</span>@enderror
                        </div>
                        <div style="display:flex;gap:10px" class="mb">
                            <div class="field" style="flex:1">
                                <label>Desde *</label>
                                <input type="date" name="fecha_inicio" required>
                                @error('fecha_inicio')<span class="err">{{ $message }}</span>@enderror
                            </div>
                            <div class="field" style="flex:1">
                                <label>Hasta *</label>
                                <input type="date" name="fecha_fin" required>
                                @error('fecha_fin')<span class="err">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div style="display:flex;gap:10px" class="mb">
                            <div class="field" style="flex:1">
                                <label>Hora inicio (opcional)</label>
                                <input type="time" name="hora_inicio">
                            </div>
                            <div class="field" style="flex:1">
                                <label>Hora fin (opcional)</label>
                                <input type="time" name="hora_fin">
                            </div>
                        </div>
                        <p class="muted mb" style="font-size:12px">Si dejas las horas vacías, se bloquea el día completo.</p>
                        <div class="field mb">
                            <label>Motivo *</label>
                            <input name="motivo" placeholder="Ej. Vacaciones, Capacitación" required>
                            @error('motivo')<span class="err">{{ $message }}</span>@enderror
                        </div>
                        <button class="btn btn-primary"><i class="fa-solid fa-ban"></i> Bloquear horario</button>
                    </form>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Médico</th><th>Fechas</th><th>Horario</th><th>Motivo</th><th></th></tr></thead>
                        <tbody>
                        @forelse($bloqueos as $b)
                            <tr>
                                <td>{{ $b['medico'] }}</td>
                                <td>
                                    @if($b['desde'] === $b['hasta'])
                                        {{ \Carbon\Carbon::parse($b['desde'])->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($b['desde'])->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($b['hasta'])->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($b['hora_inicio'] === '00:00' && $b['hora_fin'] === '23:59')
                                        Todo el día
                                    @else
                                        {{ $b['hora_inicio'] }} – {{ $b['hora_fin'] }}
                                    @endif
                                </td>
                                <td>{{ $b['motivo'] }}</td>
                                <td style="text-align:right">
                                    <form method="POST" action="{{ route('bloqueos.destroy', $b['grupo']) }}" onsubmit="return confirm('¿Eliminar este bloqueo?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="empty"><i class="fa-solid fa-calendar-check"></i><p>No hay bloqueos activos.</p></div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
    .panel-bloqueos{display:none}
    .panel-bloqueos.abierto{display:block}
    </style>
@endsection
