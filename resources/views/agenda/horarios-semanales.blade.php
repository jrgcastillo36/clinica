@extends('layouts.app')
@section('title', 'Horarios de médicos')

@section('content')
    @php
        $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
        $mapDias = [1,2,3,4,5,6,0];
        $coloresDia = ['#7c3aed','#2563eb','#0d9488','#d97706','#db2777','#16a34a','#dc2626'];
        $fondosDia = ['#f5f3ff','#eff6ff','#f0fdfa','#fffbeb','#fdf2f8','#f0fdf4','#fef2f2'];
        $paletaMedicos = ['#0d9488','#1e3a8a','#ca8a04','#db2777','#9333ea','#65a30d','#dc2626','#0891b2'];
    @endphp
    <div class="page-head no-print">
        <div><h1>Horarios de médicos</h1><p>Patrón semanal de atención de cada médico, con avisos de bloqueo.</p></div>
        <div class="flex gap">
            <a href="{{ route('agenda.index') }}" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Agenda</a>
            <a href="{{ route('agenda.disponibilidad') }}" class="btn btn-light"><i class="fa-solid fa-table-cells"></i> Disponibilidad</a>
            <button onclick="window.print()" class="btn btn-light"><i class="fa-solid fa-print"></i> Imprimir</button>
        </div>
    </div>

    @if($medicosConBloqueo > 0)
        <div class="alert no-print" style="background:#fef3c7;color:#92400e">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ $medicosConBloqueo }} {{ $medicosConBloqueo === 1 ? 'médico tiene' : 'médicos tienen' }} un bloqueo de horario activo o próximo.
        </div>
    @endif

    <div class="field mb no-print" style="max-width:280px">
        <label>Filtrar por médico</label>
        <input type="text" id="filtroMedicoTexto" placeholder="Escribe un nombre..." onkeyup="filtrarTabla()">
    </div>

    <div class="card" style="padding:0;overflow:hidden">
        <table id="tablaHorarios" class="tabla-horarios">
            <thead>
                <tr>
                    <th class="col-medico">Médico</th>
                    @foreach($dias as $i => $d)
                        <th style="background:{{ $fondosDia[$i] }};border-top:3px solid {{ $coloresDia[$i] }};color:{{ $coloresDia[$i] }}">{{ $d }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @forelse($medicos as $m)
                @php $bloqueo = $bloqueosPorMedico->get($m->id); $colorM = $paletaMedicos[$m->id % count($paletaMedicos)]; @endphp
                <tr class="fila-medico">
                    <td class="col-medico">
                        <div class="flex gap" style="align-items:center">
                            <span class="avatar-medico" style="background:{{ $colorM }}">{{ mb_substr($m->name,0,1) }}</span>
                            <div>
                                <b>{{ $m->name }}</b>
                                @if($bloqueo)
                                    <div class="pill amber" style="margin-top:4px;font-size:10.5px" title="{{ $bloqueo['motivo'] }}">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        {{ \Carbon\Carbon::parse($bloqueo['desde'])->format('d/m') }}
                                        @if($bloqueo['desde'] != $bloqueo['hasta']) – {{ \Carbon\Carbon::parse($bloqueo['hasta'])->format('d/m') }}@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    @foreach($mapDias as $i => $diaNum)
                        @php $horariosDia = $m->horarios->where('dia_semana', $diaNum) @endphp
                        <td style="background:{{ $horariosDia->isNotEmpty() ? $fondosDia[$i] : 'transparent' }}">
                            @forelse($horariosDia as $h)
                                <div class="franja-horario" style="border-left-color:{{ $colorM }}">{{ substr($h->hora_inicio,0,5) }}–{{ substr($h->hora_fin,0,5) }}</div>
                            @empty
                                <span class="sin-horario">—</span>
                            @endforelse
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty"><i class="fa-solid fa-user-doctor"></i><p>No hay médicos registrados.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <style>
    .tabla-horarios{width:100%;border-collapse:collapse}
    .tabla-horarios th{padding:12px 10px;font-size:11.5px;text-transform:uppercase;letter-spacing:.4px;font-weight:700;text-align:left}
    .tabla-horarios td{padding:10px;border-bottom:1px solid var(--line);vertical-align:middle}
    .tabla-horarios tbody tr:hover{background:#faf9ff}
    .col-medico{min-width:190px;background:#fff!important}
    .avatar-medico{width:34px;height:34px;border-radius:50%;color:#fff;font-weight:700;font-size:14px;
        display:flex;align-items:center;justify-content:center;flex:0 0 34px}
    .franja-horario{font-size:11.5px;font-weight:600;padding:3px 8px;border-left:3px solid;background:#fff;
        border-radius:6px;white-space:nowrap;margin-bottom:2px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
    .sin-horario{color:#cbd5e1;font-size:13px}
    @media print {
        .no-print, .sidebar, .topbar { display: none !important; }
        .content { padding: 0 !important; }
    }
    </style>

    @push('scripts')
    <script>
    function filtrarTabla(){
        const q = document.getElementById('filtroMedicoTexto').value.trim().toLowerCase();
        document.querySelectorAll('#tablaHorarios .fila-medico').forEach(function (fila) {
            const nombre = fila.querySelector('.col-medico b').textContent.toLowerCase();
            fila.style.display = nombre.includes(q) ? '' : 'none';
        });
    }
    </script>
    @endpush
@endsection