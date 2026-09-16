@extends('portal.layout')
@section('title','Reprogramar cita')
@section('content')
    <div class="flex between mb"><h1 style="margin:0">Reprogramar cita</h1><a href="{{ route('portal.dashboard') }}" class="btn btn-ghost btn-sm">Volver</a></div>

    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <div class="card mb">
        <p class="muted" style="margin:0"><b>Cita actual:</b> {{ $cita->fecha->locale('es')->isoFormat('D MMM YYYY') }} · {{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }}
        · {{ $cita->especialidad->nombre ?? 'General' }} · {{ $cita->medico->name ?? 'Sin médico' }}</p>
    </div>

    <form method="POST" action="{{ route('portal.cita.actualizar', $cita) }}" class="card mb" id="formReprogramar">
        @csrf @method('PUT')
        <div class="form-grid">
            <div class="field"><label>Nueva fecha *</label><input type="date" name="fecha" id="selFecha" min="{{ now()->toDateString() }}" value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}" required></div>
            <div class="field" style="align-self:flex-end">
                <button type="button" class="btn btn-light" id="btnVerHorarios" style="width:100%"><i class="fa-solid fa-magnifying-glass"></i> Ver horarios</button>
            </div>
            <div class="field full">
                <label>Nueva hora *</label>
                <input type="hidden" name="hora" id="horaElegida" value="{{ old('hora', \Illuminate\Support\Str::of($cita->hora)->substr(0,5)) }}">
                <div id="chipsHorarios" class="chips-horario">
                    <p class="muted" style="font-size:13px">Presiona "Ver horarios" para ver las horas disponibles.</p>
                </div>
            </div>
        </div>
        <div class="mt"><button class="btn btn-primary" id="btnGuardar"><i class="fa-solid fa-calendar-check"></i> Guardar cambios</button></div>
    </form>

    <form method="POST" action="{{ route('portal.cita.cancelar', $cita) }}" onsubmit="return confirm('¿Cancelar esta cita?')">
        @csrf
        <button class="btn btn-danger"><i class="fa-solid fa-ban"></i> Cancelar esta cita</button>
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
        const selFecha = document.getElementById('selFecha');
        const chipsWrap = document.getElementById('chipsHorarios');
        const horaInput = document.getElementById('horaElegida');
        const btnVer = document.getElementById('btnVerHorarios');
        const horaActual = horaInput.value;

        function pintarChips(horas) {
            chipsWrap.innerHTML = '';
            if (!horas.length) {
                chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">No hay horarios disponibles ese día. Prueba otra fecha.</p>';
                return;
            }
            horas.forEach(function (h) {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'chip-hora';
                chip.textContent = h;
                if (h === horaActual) chip.classList.add('elegido');
                chip.addEventListener('click', function () {
                    document.querySelectorAll('.chip-hora').forEach(c => c.classList.remove('elegido'));
                    chip.classList.add('elegido');
                    horaInput.value = h;
                });
                chipsWrap.appendChild(chip);
            });
        }

        function cargarFranjas() {
            if (!selFecha.value) return;
            chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">Buscando horarios...</p>';
            fetch('{{ route('portal.cita.franjas', $cita) }}?fecha=' + selFecha.value)
                .then(r => r.json())
                .then(pintarChips)
                .catch(function () {
                    chipsWrap.innerHTML = '<p class="muted" style="font-size:13px">No se pudo cargar los horarios. Intenta de nuevo.</p>';
                });
        }

        btnVer.addEventListener('click', cargarFranjas);
        selFecha.addEventListener('change', cargarFranjas);
    });
    </script>
    @endpush
@endsection