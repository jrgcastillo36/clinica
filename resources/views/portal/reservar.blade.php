@extends('portal.layout')
@section('title','Reservar cita')
@section('content')
    <div class="flex between mb"><h1 style="margin:0">Reservar una cita</h1><a href="{{ route('portal.dashboard') }}" class="btn btn-ghost btn-sm">Volver</a></div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('portal.reservar.store') }}" class="card" id="formReserva">
        @csrf
        <div class="form-grid">
            <div class="field"><label>Especialidad</label>
                <select name="especialidad_id">
                    <option value="">— Indiferente —</option>
                    @foreach($especialidades as $e)<option value="{{ $e->id }}" @selected(old('especialidad_id')==$e->id)>{{ $e->nombre }}</option>@endforeach
                </select></div>
            <div class="field"><label>Psicólogo(a) *</label>
                <select name="medico_id" id="selMedico" required>
                    <option value="">— Elige un psicólogo(a) —</option>
                    @foreach($medicos as $m)<option value="{{ $m->id }}" @selected(old('medico_id')==$m->id)>{{ $m->titulo_profesional ? $m->titulo_profesional.' ' : '' }}{{ $m->name }}</option>@endforeach
                </select></div>
<div class="field"><label>Fecha *</label><input type="date" name="fecha" id="selFecha" min="{{ now()->toDateString() }}" value="{{ old('fecha', now()->toDateString()) }}" required></div>
<div class="field" style="align-self:flex-end">
    <button type="button" class="btn btn-light" id="btnVerHorarios" style="width:100%"><i class="fa-solid fa-magnifying-glass"></i> Ver horarios</button>
</div>            <div class="field full">
                <label>Hora *</label>
                <input type="hidden" name="hora" id="horaElegida" value="{{ old('hora') }}">
                <div id="chipsHorarios" class="chips-horario">
                    <p class="muted" style="font-size:13px">Elige un psicólogo(a) y una fecha para ver los horarios disponibles.</p>
                </div>
            </div>
            <div class="field full"><label>Motivo (opcional)</label><input name="motivo" value="{{ old('motivo') }}" placeholder="Ej. seguimiento, primera cita..."></div>
        </div>
        <div class="mt"><button class="btn btn-primary" id="btnSolicitar" disabled><i class="fa-solid fa-calendar-check"></i> Solicitar cita</button></div>
        <p class="muted mt" style="font-size:12px">Cada sesión dura 90 minutos. Los horarios mostrados ya descuentan lo que esté ocupado.</p>
    </form>
    <style>
    .chips-horario{display:flex;flex-wrap:wrap;gap:8px;padding:6px 0}
    .chip-hora{background:var(--bg-pink);border:1.5px solid transparent;border-radius:10px;padding:9px 16px;
        font-size:13.5px;font-weight:600;cursor:pointer;color:var(--ink);transition:.15s}
    .chip-hora:hover{border-color:var(--violet-2)}
    .chip-hora.elegido{background:var(--grad);color:#fff;border-color:transparent}
    </style>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selMedico = document.getElementById('selMedico');
        const selFecha = document.getElementById('selFecha');
        const chipsWrap = document.getElementById('chipsHorarios');
        const horaInput = document.getElementById('horaElegida');
        const btnSolicitar = document.getElementById('btnSolicitar');
        const btnVer = document.getElementById('btnVerHorarios');

        function actualizarBoton() {
            btnSolicitar.disabled = !horaInput.value;
        }

        function pintarChips(horas) {
            chipsWrap.innerHTML = '';
            horaInput.value = '';
            actualizarBoton();

            if (!horas.length) {
                chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">Ese psicólogo(a) no tiene horarios disponibles ese día. Prueba otra fecha.</p>';
                return;
            }
            horas.forEach(function (h) {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'chip-hora';
                chip.textContent = h;
                chip.addEventListener('click', function () {
                    document.querySelectorAll('.chip-hora').forEach(c => c.classList.remove('elegido'));
                    chip.classList.add('elegido');
                    horaInput.value = h;
                    actualizarBoton();
                });
                chipsWrap.appendChild(chip);
            });
        }

        function cargarFranjas() {
            if (!selMedico.value || !selFecha.value) {
                chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">Elige un psicólogo(a) y una fecha, luego presiona "Ver horarios".</p>';
                return;
            }
            chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">Buscando horarios...</p>';
            fetch('{{ route('portal.reservar.franjas') }}?medico_id=' + selMedico.value + '&fecha=' + selFecha.value)
                .then(r => r.json())
                .then(pintarChips)
                .catch(function () {
                    chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">No se pudo cargar los horarios. Intenta de nuevo.</p>';
                });
        }

        btnVer.addEventListener('click', cargarFranjas);
        selMedico.addEventListener('change', cargarFranjas);
        selFecha.addEventListener('change', cargarFranjas);
    });
    </script>
    @endpush
@endsection