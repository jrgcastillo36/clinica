@extends('layouts.app')
@section('title', $cita->exists ? 'Editar cita' : 'Nueva cita')

@section('content')
    <div class="page-head">
        <div><h1>{{ $cita->exists ? 'Editar cita' : 'Nueva cita' }}</h1><p>Programa la atención del paciente.</p></div>
        <a href="{{ route('citas.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <form method="POST" action="{{ $cita->exists ? route('citas.update',$cita) : route('citas.store') }}" class="card">
        @csrf
        @if($cita->exists) @method('PUT') @endif
        <div class="form-grid">
            <div class="field" style="position:relative">
                <label>Paciente *</label>
                <input type="text" id="pacienteBuscar" autocomplete="off"
                    placeholder="Busca por nombre o DNI..."
                    value="{{ old('paciente_nombre', $cita->exists ? $cita->paciente->nombre_completo.' — '.$cita->paciente->documento : '') }}">
                <input type="hidden" name="paciente_id" id="pacienteId" value="{{ old('paciente_id', $cita->paciente_id) }}">
                <div id="pacienteLista" class="paciente-lista"></div>
                @error('paciente_id')<span class="err">{{ $message }}</span>@enderror
            </div>

            <div class="field"><label>Especialidad</label>
                <select name="especialidad_id">
                    <option value="">—</option>
                    @foreach($especialidades as $e)
                        <option value="{{ $e->id }}" @selected(old('especialidad_id',$cita->especialidad_id)==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Médico</label>
                <select name="medico_id">
                    <option value="">— Sin asignar —</option>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id }}" @selected(old('medico_id',$cita->medico_id)==$m->id)>{{ $m->name }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Estado</label>
                <select name="estado" required>
                    @foreach(['pendiente'=>'Pendiente','confirmada'=>'Confirmada','atendida'=>'Atendida','cancelada'=>'Cancelada','no_asistio'=>'No asistió'] as $k=>$v)
                        <option value="{{ $k }}" @selected(old('estado',$cita->estado ?? 'pendiente')==$k)>{{ $v }}</option>
                    @endforeach
                </select></div>
            <div class="field"><label>Fecha *</label><input type="date" name="fecha" value="{{ old('fecha', optional($cita->fecha)->format('Y-m-d') ?? $cita->fecha) }}" required></div>
            <div class="field"><label>Hora *</label><input type="time" name="hora" value="{{ old('hora', \Illuminate\Support\Str::of($cita->hora)->substr(0,5)) }}" required></div>
            <div class="field"><label>Hora *</label><input type="time" name="hora" value="{{ old('hora', \Illuminate\Support\Str::of($cita->hora)->substr(0,5)) }}" required>@error('hora')<span class="err">{{ $message }}</span>@enderror</div>
            <div class="field"><label>Modalidad</label>
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-top:6px">
                    <input type="checkbox" name="es_teleconsulta" value="1" @checked(old('es_teleconsulta',$cita->es_teleconsulta ?? false))>
                    <span>Teleconsulta (videollamada)</span>
                </label>
            </div>
            <div class="field full"><label>Motivo</label><input name="motivo" value="{{ old('motivo',$cita->motivo) }}"></div>
            <div class="field full"><label>Notas</label><textarea name="notas">{{ old('notas',$cita->notas) }}</textarea></div>
        </div>
        <div class="mt"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cita</button></div>
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
        // Preparar los datos de pacientes para JavaScript usando función anónima tradicional
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
    </script>
    @endpush
@endsection