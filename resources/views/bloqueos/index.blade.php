@extends('layouts.app')
@section('title', 'Bloqueos de horario')

@section('content')
    <div class="page-head">
        <div><h1>Bloqueos de horario</h1><p>Vacaciones, capacitaciones y días no disponibles por médico.</p></div>
        <a href="{{ route('agenda.index') }}" class="btn btn-ghost"><i class="fa-solid fa-calendar-days"></i> Ver Agenda</a>
    </div>

    <div class="grid g-2" style="grid-template-columns:1fr 1.4fr">
        <div class="card">
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
                <thead><tr><th>Psicólogo(a)</th><th>Fechas</th><th>Horario</th><th>Motivo</th><th></th></tr></thead>
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
                            <form method="POST" action="{{ route('bloqueos.destroy', $b['grupo']) }}" onsubmit="return confirm('¿Eliminar este bloqueo? El médico volverá a estar disponible en esas fechas.')">
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
@endsection
