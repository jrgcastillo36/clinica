@extends('layouts.app')
@section('title', 'Agenda')

@section('content')
    <div class="agenda-layout">
        @unless(auth()->user()->isMedico())
        <aside class="agenda-sidebar">
            <div id="miniCalendar"></div>
            <div class="agenda-sidebar-medicos">
                <div class="agenda-sidebar-titulo">
                    <i class="fa-solid fa-user-doctor"></i> Médicos
                    <div class="filtro-medicos-acciones">
                        <button type="button" onclick="marcarTodosMedicos(true)">Todos</button>
                        <button type="button" onclick="marcarTodosMedicos(false)">Ninguno</button>
                    </div>
                </div>
                @foreach($medicos as $m)
                    <label class="chip-medico">
                        <input type="checkbox" class="chkMedico" value="{{ $m->id }}" checked>
                        <span class="chip-dot" data-medico="{{ $m->id }}"></span>
                        {{ $m->name }}
                    </label>
                @endforeach
            </div>
        </aside>
        @endunless

        <div class="agenda-main">
            <div class="page-head">
                <div><h1>Agenda</h1><p>Calendario de citas · arrastra una cita para reprogramarla.</p></div>
                @unless(auth()->user()->isMedico())
                <a href="{{ route('agenda.disponibilidad') }}" class="btn btn-light"><i class="fa-solid fa-table-cells"></i> Disponibilidad</a>
                <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Nueva cita</a>
                @endunless
            </div>

            {{-- Resumen rápido --}}
            <div class="grid g-4 mb ag-stats">
                <div class="ag-stat"><div class="ag-ic" style="background:#ede9fe;color:#7c3aed"><i class="fa-solid fa-calendar-day"></i></div>
                    <div><div class="ag-num" id="stHoy">0</div><div class="ag-cap">Citas hoy</div></div></div>
                <div class="ag-stat"><div class="ag-ic" style="background:#dbeafe;color:#2563eb"><i class="fa-solid fa-calendar-week"></i></div>
                    <div><div class="ag-num" id="stSemana">0</div><div class="ag-cap">Esta semana</div></div></div>
                <div class="ag-stat"><div class="ag-ic" style="background:#fef3c7;color:#b45309"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div><div class="ag-num" id="stPend">0</div><div class="ag-cap">Pendientes</div></div></div>
                <div class="ag-stat"><div class="ag-ic" style="background:#fce7f3;color:#be185d"><i class="fa-solid fa-calendar-check"></i></div>
                    <div><div class="ag-num" id="stMes">0</div><div class="ag-cap">En el mes</div></div></div>
            </div>

            {{-- Leyenda --}}
            <div class="ag-legend mb">
                <span><i style="background:#f59e0b"></i> Pendiente</span>
                <span><i style="background:#3b82f6"></i> Confirmada</span>
                <span><i style="background:#22c55e"></i> Atendida</span>
                <span><i style="background:#ef4444"></i> Cancelada</span>
                <span><i style="background:#94a3b8"></i> No asistió</span>
            </div>

            <input type="date" id="saltarFecha" style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px">
            <div class="card ag-card"><div id="calendar"></div></div>
        </div>
    </div>

    <div id="nuevaCitaModalFondo" class="cita-modal-fondo" onclick="cerrarNuevaCitaModal(event)">
        <div class="cita-modal">
            <button class="cita-modal-cerrar" onclick="cerrarNuevaCitaModal()"><i class="fa-solid fa-xmark"></i></button>
            <h3>Nueva cita</h3>
            <form id="formNuevaCitaRapida">
                <div class="field" style="position:relative;margin-bottom:12px">
                    <label>Paciente *</label>
                    <input type="text" id="ncBuscarPaciente" autocomplete="off" placeholder="Busca por nombre o DNI...">
                    <input type="hidden" id="ncPacienteId">
                    <div id="ncPacienteLista" class="paciente-lista"></div>
                </div>
                <div style="display:flex;gap:10px;margin-bottom:12px">
                    <div class="field" style="flex:1">
                        <label>Fecha *</label>
                        <input type="date" id="ncFecha" required>
                    </div>
                    <div class="field" style="flex:1">
                        <label>Hora *</label>
                        <input type="time" id="ncHora" required>
                    </div>
                </div>
                               <div style="display:flex;gap:10px;margin-bottom:12px">
                    <div class="field" style="flex:1">
                        <label>Médico</label>
                        <select id="ncMedico">
                            <option value="">— Sin asignar —</option>
                            @foreach($medicos as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="flex:1">
                        <label>Consultorio</label>
                        <select id="ncConsultorio">
                            <option value="">— Sin asignar —</option>
                            @foreach($consultorios as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="ncError" style="color:var(--danger);font-size:12.5px;margin-bottom:10px;display:none"></div>
                <div style="display:flex;gap:10px;align-items:center;justify-content:space-between">
                    <a href="#" id="ncMasOpciones" style="font-size:12.5px;font-weight:600;color:var(--violet)">Más opciones →</a>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="citaModalFondo" class="cita-modal-fondo" onclick="cerrarModalCita(event)">
        <div class="cita-modal">
            <button class="cita-modal-cerrar" onclick="cerrarModalCita()"><i class="fa-solid fa-xmark"></i></button>
            <h3 id="cmTitulo"></h3>
            <div id="cmEstado" class="cm-estado"></div>
            <div class="cm-datos" id="cmDatos"></div>
            <div class="cm-acciones" id="cmAcciones"></div>
        </div>
    </div>

    <style>
    .agenda-layout{display:flex;gap:20px;align-items:flex-start}
    .agenda-sidebar{flex:0 0 250px;background:#fff;border-radius:16px;box-shadow:var(--shadow);padding:14px;position:sticky;top:16px}
    .agenda-sidebar .fc{font-size:11px}
    .agenda-sidebar .fc-toolbar-title{font-size:13px!important}
    .agenda-sidebar .fc-button{padding:3px 8px!important;font-size:11px!important}
    .agenda-sidebar .fc-daygrid-day-number{font-size:10px!important;margin:2px!important;padding:1px 5px!important;color:#1f2937!important;background:transparent!important;font-weight:700!important}
    .agenda-sidebar .fc-daygrid-day-frame{min-height:28px!important}
    .agenda-sidebar .fc-daygrid-event{display:none}
    .agenda-sidebar .fc-col-header-cell-cushion{font-size:9px!important;padding:4px 2px!important;color:var(--ink-soft)!important}
    .agenda-sidebar .fc-col-header-cell{background:transparent!important;border-top:none!important}
    .agenda-sidebar .fc-daygrid-day.fc-day-sun,.agenda-sidebar .fc-daygrid-day.fc-day-sat{background:transparent!important}
    .agenda-sidebar .fc-day-today .fc-daygrid-day-number{background:var(--grad)!important;color:#fff!important;border-radius:50%;width:20px!important;height:20px!important;display:inline-flex!important;align-items:center;justify-content:center}
    .agenda-sidebar .mini-seleccionado:not(.fc-day-today) .fc-daygrid-day-number{background:#e5e7eb!important;color:#1f2937!important;border-radius:50%;width:20px!important;height:20px!important;display:inline-flex!important;align-items:center;justify-content:center;font-weight:800!important}
    .agenda-sidebar-medicos{margin-top:16px;padding-top:14px;border-top:1px solid var(--line);max-height:360px;overflow-y:auto}
    .agenda-sidebar-titulo{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--ink-soft);
        text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px;flex-wrap:wrap}
    .agenda-main{flex:1;min-width:0}
    @media(max-width:1000px){.agenda-layout{flex-direction:column}.agenda-sidebar{position:static;width:100%}}

    .ag-stats{gap:14px}
    .ag-stat{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px 18px;box-shadow:0 4px 14px rgba(90,70,160,.05)}
    .ag-ic{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;font-size:19px;flex:0 0 46px}
    .ag-num{font-size:24px;font-weight:700;color:var(--ink);line-height:1}
    .ag-cap{font-size:12px;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.4px;margin-top:3px}
    .ag-legend{display:flex;gap:16px;flex-wrap:wrap;font-size:12.5px;color:var(--ink-soft);font-weight:500}
    .ag-legend span{display:flex;align-items:center;gap:6px}
    .ag-legend i{width:12px;height:12px;border-radius:4px;display:inline-block}
    .ag-card{padding:18px 18px 8px}

    .fc{--fc-border-color:#efeaf7;--fc-today-bg-color:#faf5ff;--fc-page-bg-color:#fff;font-family:inherit}
    .fc .fc-toolbar-title{font-size:20px;font-weight:700;color:var(--ink);text-transform:capitalize}
    .fc .fc-toolbar.fc-header-toolbar{margin-bottom:16px;flex-wrap:wrap;gap:8px}
    .fc .fc-button{background:var(--bg-pink);border:none;color:var(--violet);font-weight:600;font-size:13px;
        padding:8px 14px;border-radius:12px;text-transform:capitalize;box-shadow:none;transition:.15s}
    .fc .fc-button:hover{background:#f0e7ff;color:var(--violet)}
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active{background:linear-gradient(135deg,var(--violet-2),var(--pink));color:#fff}
    .fc .fc-today-button{background:linear-gradient(135deg,var(--violet-2),var(--pink));color:#fff}
    .fc .fc-today-button:disabled{opacity:.5}
    .fc .fc-button .fc-icon{font-size:15px}
    .fc .fc-button-group{gap:6px;display:inline-flex}
    .fc-theme-standard .fc-scrollgrid{border-radius:14px;overflow:hidden;border:1px solid var(--line)}
    .fc .fc-col-header-cell{padding:0;border-top:3px solid transparent}
    .fc .fc-col-header-cell-cushion{font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;padding:10px 6px;display:block;width:100%}
    .fc .fc-col-header-cell.fc-day-sun{background:#fef2f2;border-top-color:#ef4444}
    .fc .fc-col-header-cell.fc-day-mon{background:#f5f3ff;border-top-color:#7c3aed}
    .fc .fc-col-header-cell.fc-day-tue{background:#eff6ff;border-top-color:#2563eb}
    .fc .fc-col-header-cell.fc-day-wed{background:#f0fdfa;border-top-color:#0d9488}
    .fc .fc-col-header-cell.fc-day-thu{background:#fffbeb;border-top-color:#d97706}
    .fc .fc-col-header-cell.fc-day-fri{background:#fdf2f8;border-top-color:#db2777}
    .fc .fc-col-header-cell.fc-day-sat{background:#f0fdf4;border-top-color:#16a34a}
    .fc .fc-day-sun  .fc-col-header-cell-cushion{color:#dc2626}
    .fc .fc-day-mon  .fc-col-header-cell-cushion{color:#7c3aed}
    .fc .fc-day-tue  .fc-col-header-cell-cushion{color:#2563eb}
    .fc .fc-day-wed  .fc-col-header-cell-cushion{color:#0d9488}
    .fc .fc-day-thu  .fc-col-header-cell-cushion{color:#d97706}
    .fc .fc-day-fri  .fc-col-header-cell-cushion{color:#db2777}
    .fc .fc-day-sat  .fc-col-header-cell-cushion{color:#16a34a}
    .fc .fc-daygrid-day-top{flex-direction:row;justify-content:flex-end}
    .fc .fc-daygrid-day-number{font-size:13px;font-weight:800;margin:6px;padding:3px 9px;border-radius:9px;min-width:26px;text-align:center;color:#5b4b86;background:#f2eefb;line-height:1.2}
    .fc .fc-day-sun  .fc-daygrid-day-number{color:#dc2626;background:#fde3e3}
    .fc .fc-day-mon  .fc-daygrid-day-number{color:#6d28d9;background:#ece7fd}
    .fc .fc-day-tue  .fc-daygrid-day-number{color:#1d4ed8;background:#dbeafe}
    .fc .fc-day-wed  .fc-daygrid-day-number{color:#0f766e;background:#cdf5ec}
    .fc .fc-day-thu  .fc-daygrid-day-number{color:#b45309;background:#fdecc8}
    .fc .fc-day-fri  .fc-daygrid-day-number{color:#be185d;background:#fbdcec}
    .fc .fc-day-sat  .fc-daygrid-day-number{color:#15803d;background:#d6f5df}
    .fc .fc-daygrid-day.fc-day-sun{background:#fffbfb}
    .fc .fc-daygrid-day.fc-day-sat{background:#fbfefc}
    .fc .fc-daygrid-day.fc-day-today{background:#faf5ff!important}
    .fc .fc-day-today .fc-daygrid-day-number{background:linear-gradient(135deg,var(--violet-2),var(--pink))!important;color:#fff!important;border-radius:50%;width:26px;height:26px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin:6px 8px 6px 6px;box-shadow:0 3px 8px rgba(168,85,247,.4)}
    .fc .fc-daygrid-day-frame{min-height:96px}
    .fc .fc-daygrid-event,.fc .fc-timegrid-event{background:transparent!important;border:none!important;box-shadow:none!important;margin:2px 4px!important}
    .fc .fc-daygrid-event-harness{margin-top:1px}
    .ev{display:flex;align-items:center;gap:6px;padding:4px 8px;border-radius:8px;font-size:11.5px;font-weight:600;overflow:hidden;white-space:nowrap;transition:.12s;background:#f1f5f9;color:#1f2937}
    .ev:hover{transform:translateX(2px)}
    .ev .ev-dot{width:7px;height:7px;border-radius:50%;flex:0 0 7px}
    .ev .ev-time{font-weight:700;opacity:.9}
    .ev .ev-title{overflow:hidden;text-overflow:ellipsis;min-width:0}
    .ev-pendiente{background:#fef3c7;color:#92400e}
    .ev-confirmada{background:#dbeafe;color:#1e40af}
    .ev-atendida{background:#dcfce7;color:#166534}
    .ev-cancelada{background:#fee2e2;color:#991b1b}
    .ev-no_asistio{background:#f1f5f9;color:#475569}
    .fc .fc-timegrid-event .ev{white-space:normal}
    .fc .fc-more-link{color:var(--violet);font-weight:600;font-size:11px}
    .ev-med-dot{width:7px;height:7px;border-radius:50%;flex:0 0 7px;margin-left:auto}
    .ev-dia{flex-direction:column;align-items:flex-start;gap:3px;white-space:normal;padding:8px 10px;height:100%;box-sizing:border-box}
    .ev-dia-top{display:flex;align-items:center;gap:6px;font-size:12.5px}
    .ev-linea{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:500;opacity:.9}
    .ev-linea i{width:12px;font-size:10px}
        .ev-bloqueo{background:repeating-linear-gradient(45deg,#94a3b8,#94a3b8 6px,#cbd5e1 6px,#cbd5e1 12px)!important;
        color:#1f2937!important;font-weight:700;justify-content:center}
       

        .fc-event-mirror .ev,.fc-event-dragging .ev{background:var(--med-solid,#3b82f6)!important;color:#fff!important;
        box-shadow:0 4px 14px rgba(0,0,0,.25)}
    .fc-event-mirror .ev-linea,.fc-event-dragging .ev-linea{color:#fff!important;opacity:.95}
    .cita-tooltip{position:absolute;z-index:9999;background:#1f2937;color:#fff;padding:10px 14px;
        border-radius:10px;font-size:12.5px;line-height:1.7;box-shadow:0 10px 30px rgba(0,0,0,.25);
        max-width:240px;pointer-events:none}
    .cita-tooltip b{font-size:13.5px;display:block;margin-bottom:4px}
    .cita-tooltip i{width:14px;opacity:.75;margin-right:4px}
    .cita-tooltip .tt-estado{margin-top:6px;font-weight:700;text-transform:uppercase;font-size:10.5px;letter-spacing:.4px;opacity:.85}
    .filtro-medicos-acciones{display:flex;gap:6px;margin-left:auto}
    .filtro-medicos-acciones button{background:var(--bg-pink);border:none;border-radius:8px;
        padding:3px 8px;font-size:10px;font-weight:600;cursor:pointer;color:var(--violet)}
    .chip-medico{display:flex!important;align-items:center;gap:8px;padding:7px 8px;border-radius:10px;
        font-size:12.5px;font-weight:600;cursor:pointer;margin-bottom:3px;
        border:1.5px solid #e5e7eb;color:#9ca3af;transition:.15s}
    .chip-medico:has(.chkMedico:checked){color:#1f2937}
    .chip-medico input[type=checkbox]{display:none}
    .chip-dot{width:10px;height:10px;border-radius:50%;flex:0 0 10px;border:2px solid #e5e7eb}
    .cita-modal-fondo{display:none;position:fixed;inset:0;background:rgba(15,20,35,.5);z-index:200;
        align-items:center;justify-content:center;padding:20px}
    .cita-modal-fondo.abierto{display:flex}
    .cita-modal{background:#fff;border-radius:20px;padding:26px;max-width:380px;width:100%;
        box-shadow:0 24px 60px rgba(0,0,0,.3);position:relative}
    .cita-modal-cerrar{position:absolute;top:16px;right:16px;background:var(--bg-pink);border:none;
        width:32px;height:32px;border-radius:10px;cursor:pointer;color:var(--ink-soft)}
    .cita-modal h3{margin:0 0 8px;font-size:18px;padding-right:30px}
    .cm-estado{margin-bottom:14px}
    .cm-datos{display:flex;flex-direction:column;gap:9px;font-size:13.5px;color:var(--ink);margin-bottom:20px}
    .cm-datos div{display:flex;align-items:center;gap:10px}
    .cm-datos i{width:16px;color:var(--violet-2)}
    .cm-acciones{display:flex;gap:8px;flex-wrap:wrap}
    .paciente-lista{display:none;position:absolute;z-index:20;background:#fff;border:1px solid var(--line);
        border-radius:10px;margin-top:4px;max-height:200px;overflow-y:auto;width:100%;
        box-shadow:0 8px 20px rgba(0,0,0,.1)}
    .paciente-lista div{padding:9px 14px;cursor:pointer;font-size:13.5px}
    .paciente-lista div:hover{background:var(--bg-pink)}
    .fc .fc-timegrid-col.fc-day-sun{background:#fffbfb}
    .fc .fc-timegrid-col.fc-day-mon{background:#fbfaff}
    .fc .fc-timegrid-col.fc-day-tue{background:#fafcff}
    .fc .fc-timegrid-col.fc-day-wed{background:#f8fefd}
    .fc .fc-timegrid-col.fc-day-thu{background:#fffdf6}
    .fc .fc-timegrid-col.fc-day-fri{background:#fffafd}
    .fc .fc-timegrid-col.fc-day-sat{background:#fbfefc}
    .fc .fc-timegrid-col.fc-day-today{background:#faf5ff!important}
    .fc .fc-timegrid-slot-label-cushion,.fc .fc-timegrid-axis-cushion{color:#9a8fbf;font-size:11px;font-weight:700}
    .fc .fc-timegrid-slot{height:2.4em}
    .fc .fc-timegrid-now-indicator-line{border-color:#ec4899}
    .fc .fc-timegrid-now-indicator-arrow{border-color:#ec4899;background:#ec4899}
    .fc .fc-timegrid-event{border-radius:8px;padding:1px 2px}
    .fc .fc-timegrid-event .ev{padding:3px 6px}
    .fc .fc-timeGridWeek-view .fc-col-header-cell-cushion,
    .fc .fc-timeGridDay-view .fc-col-header-cell-cushion{font-size:12.5px;line-height:1.4}
    [data-theme="dark"] .ag-stat,[data-theme="dark"] .fc,[data-theme="dark"] .agenda-sidebar{background:#161428}
    @media(max-width:640px){ .fc .fc-toolbar.fc-header-toolbar{justify-content:center} .fc .fc-toolbar-title{font-size:16px} }
    </style>

    @push('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('calendar');
        const token = document.querySelector('meta[name=csrf-token]').content;

        function esHoy(d){ const t=new Date(); return d.getFullYear()===t.getFullYear() && d.getMonth()===t.getMonth() && d.getDate()===t.getDate(); }
        function enSemana(d){ const t=new Date(); const day=(t.getDay()+6)%7; const ini=new Date(t); ini.setHours(0,0,0,0); ini.setDate(t.getDate()-day); const fin=new Date(ini); fin.setDate(ini.getDate()+7); return d>=ini && d<fin; }

        const paletaMedicos = ['#0d9488','#1e3a8a','#ca8a04','#db2777','#9333ea','#65a30d','#dc2626','#0891b2'];
        const paletaMedicosBg = ['#ccfbf1','#dbeafe','#fef9c3','#fce7f3','#f3e8ff','#ecfccb','#fee2e2','#cffafe'];
        function colorMedico(medicoId){ return paletaMedicos[medicoId % paletaMedicos.length]; }
        function colorMedicoBg(medicoId){ return paletaMedicosBg[medicoId % paletaMedicosBg.length]; }

        let tooltipEl = null;
        function mostrarTooltip(event, mouseEvent){
            const p = event.extendedProps;
            ocultarTooltip();
            tooltipEl = document.createElement('div');
            tooltipEl.className = 'cita-tooltip';
            tooltipEl.style.visibility = 'hidden';
                       const rangoTooltip = p.hora + (p.horaFin ? ' – ' + p.horaFin : '');
            tooltipEl.innerHTML =
                '<b>' + event.title + '</b>' +
                (p.hora ? '<div><i class="fa-regular fa-clock"></i> ' + rangoTooltip + '</div>' : '') +
                (p.especialidad ? '<div><i class="fa-solid fa-stethoscope"></i> ' + p.especialidad + '</div>' : '') +
                (p.medico ? '<div><i class="fa-solid fa-user-doctor"></i> ' + p.medico + '</div>' : '') +
                (p.consultorio ? '<div><i class="fa-solid fa-door-open"></i> ' + p.consultorio + '</div>' : '') +
                (p.telefono ? '<div><i class="fa-solid fa-phone"></i> ' + p.telefono + '</div>' : '') +
                (p.motivo ? '<div><i class="fa-regular fa-note-sticky"></i> ' + p.motivo + '</div>' : '') +
                '<div class="tt-estado">' + (p.estadoLabel || '') + '</div>';
            document.body.appendChild(tooltipEl);

            const rect = mouseEvent.target.closest('.fc-event').getBoundingClientRect();
            const ttRect = tooltipEl.getBoundingClientRect();
            const margen = 8;

            let top = rect.bottom + window.scrollY + 6;
            if (rect.bottom + ttRect.height + margen > window.innerHeight) {
                top = rect.top + window.scrollY - ttRect.height - 6;
            }

            let left = rect.left + window.scrollX;
            if (left + ttRect.width + margen > window.innerWidth) {
                left = window.innerWidth - ttRect.width - margen + window.scrollX;
            }
            if (left < margen) left = margen;

            tooltipEl.style.top = top + 'px';
            tooltipEl.style.left = left + 'px';
            tooltipEl.style.visibility = 'visible';
        }
        function ocultarTooltip(){
            if (tooltipEl) { tooltipEl.remove(); tooltipEl = null; }
        }
        function actualizarStats(cal){
            let hoy=0,sem=0,pend=0,mes=0;
            cal.getEvents().forEach(function(e){ if(!e.start) return; mes++; if(esHoy(e.start))hoy++; if(enSemana(e.start))sem++; if(e.extendedProps.estado==='pendiente')pend++; });
            document.getElementById('stHoy').textContent=hoy;
            document.getElementById('stSemana').textContent=sem;
            document.getElementById('stPend').textContent=pend;
            document.getElementById('stMes').textContent=mes;
        }

        let sincronizandoDesdeMini = false;

        const cal = new FullCalendar.Calendar(el, {
                        initialView: 'timeGridDay',
            locale: 'es',
            height: 760,
            expandRows: true,
            dayMaxEvents: 3,
            fixedWeekCount: false,
            headerToolbar: { left:'prev,next today saltarFecha', center:'title', right:'dayGridMonth,timeGridWeek,tresDias,timeGridDay,listWeek' },
                        views: {
                tresDias: { type: 'timeGrid', duration: { days: 3 }, buttonText: '3 días' }
            },
            customButtons: {
                saltarFecha: {
                    text: '📅 Ir a fecha',
                    click: function () {
                        const input = document.getElementById('saltarFecha');
                        input.value = cal.getDate().toISOString().substring(0, 10);
                        input.showPicker ? input.showPicker() : input.click();
                    }
                }
            },
            buttonText: { today:'Hoy', month:'Mes', week:'Semana', day:'Día', list:'Agenda' },

            slotMinTime: '07:00:00',
            slotMaxTime: '21:00:00',
            slotDuration: '00:30:00',
            slotLabelFormat: { hour: 'numeric', minute: '2-digit', hour12: true },

            editable: {{ auth()->user()->isMedico() ? 'false' : 'true' }},
            selectable: {{ auth()->user()->isMedico() ? 'false' : 'true' }},
            selectMirror: true,

            events: function (info, successCallback, failureCallback) {
                const seleccionados = Array.from(document.querySelectorAll('.chkMedico:checked')).map(c => c.value).join(',');
                fetch('{{ route('agenda.eventos') }}?start=' + info.startStr + '&end=' + info.endStr + '&medico_ids=' + seleccionados)
                    .then(r => r.json())
                    .then(successCallback)
                    .catch(failureCallback);
            },

                        eventContent: function(arg){
                const p = arg.event.extendedProps;
                if (p.esBloqueo) {
                    return { html: '<div class="ev ev-bloqueo"><i class="fa-solid fa-ban"></i> '+arg.event.title+'</div>' };
                }
                const esMedicoLogueado = @json(auth()->user()->isMedico());


                const estadoDot = '<span class="ev-dot" style="background:'+(arg.event.backgroundColor||'#7c3aed')+'" title="'+(p.estadoLabel||'')+'"></span>';

                if (arg.view.type === 'listWeek') {
                    const medicoTxt = (!esMedicoLogueado && p.medico) ? ' · ' + p.medico : '';
                    return { html: '<b>'+arg.event.title+'</b>'+medicoTxt+' <span style="color:var(--ink-soft)">('+ (p.estadoLabel||'') +')</span>' };
                }
                             if (arg.view.type === 'timeGridDay' || arg.view.type === 'timeGridWeek' || arg.view.type === 'tresDias') {
                    function fmtHora(d){ return d ? String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0') : ''; }
                    const rango = fmtHora(arg.event.start) + (arg.event.end ? ' – ' + fmtHora(arg.event.end) : '');
                    const medicoLinea = (!esMedicoLogueado && p.medico) ? '<div class="ev-linea"><i class="fa-solid fa-user-doctor"></i> '+p.medico+'</div>' : '';
                    const estiloColor = p.medicoId ? 'border-left:4px solid '+colorMedico(p.medicoId)+';background:'+colorMedicoBg(p.medicoId)+';--med-solid:'+colorMedico(p.medicoId)+';' : '';
                    return { html:
                        '<div class="ev ev-dia" style="'+estiloColor+'">'+
                            '<div class="ev-dia-top">'+estadoDot+'<b>'+rango+'</b></div>'+
                            '<div class="ev-linea"><i class="fa-solid fa-user"></i> '+arg.event.title+'</div>'+
                            medicoLinea+
                        '</div>' };
                }

                                const time = p.hora ? '<span class="ev-time">'+p.hora+'</span>' : '';
                const textoPrincipal = esMedicoLogueado ? arg.event.title : (p.medico || arg.event.title);
                const title = '<span class="ev-title">'+textoPrincipal+'</span>';
                const estiloColor2 = p.medicoId ? 'border-left:4px solid '+colorMedico(p.medicoId)+';background:'+colorMedicoBg(p.medicoId)+';' : '';
                return { html: '<div class="ev" style="'+estiloColor2+'">'+estadoDot+time+title+'</div>' };
            },

                             eventDidMount: function(arg){
                
                arg.el.addEventListener('mouseenter', function (ev) { mostrarTooltip(arg.event, ev); });
                arg.el.addEventListener('mouseleave', function () { ocultarTooltip(); });
            },

            eventsSet: function(){ actualizarStats(cal); },
            eventClick: function (info) { info.jsEvent.preventDefault(); abrirModalCita(info.event); },
            select: function (info) {
                const fecha = info.startStr.substring(0, 10);
                let hora = '09:00', duracion = 30;
                if (info.view.type !== 'dayGridMonth') {
                    hora = info.startStr.substring(11, 16);
                    duracion = Math.round((new Date(info.endStr) - new Date(info.startStr)) / 60000);
                    if (!duracion || duracion < 5) duracion = 30;
                }
                cal.unselect();
                abrirNuevaCitaModal(fecha, hora, duracion);
            },
                        eventDrop: function (info) {
                const e = info.event;
                const fecha = e.start.getFullYear()+'-'+String(e.start.getMonth()+1).padStart(2,'0')+'-'+String(e.start.getDate()).padStart(2,'0');
                const hora = e.start.toTimeString().slice(0,5);
                fetch('{{ url('agenda/citas') }}/' + e.id + '/mover', {
                    method:'PUT',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
                    body: JSON.stringify({ fecha: fecha, hora: hora })
                }).then(r => { if(!r.ok){ alert('No se pudo mover la cita'); info.revert(); } });
            },
            eventResize: function (info) {
                const e = info.event;
                const fecha = e.start.getFullYear()+'-'+String(e.start.getMonth()+1).padStart(2,'0')+'-'+String(e.start.getDate()).padStart(2,'0');
                const hora = e.start.toTimeString().slice(0,5);
                const duracion = Math.round((e.end - e.start) / 60000);
                fetch('{{ url('agenda/citas') }}/' + e.id + '/mover', {
                    method:'PUT',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
                    body: JSON.stringify({ fecha: fecha, hora: hora, duracion: duracion })
                }).then(async function (r) {
                    if (!r.ok) {
                        const data = await r.json().catch(function () { return {}; });
                        alert(data.mensaje || 'No se pudo cambiar la duración');
                        info.revert();
                    }
                });
            }
        });

        const miniCal = new FullCalendar.Calendar(document.getElementById('miniCalendar'), {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: { left:'prev,next', center:'title', right:'' },
            height: 230,
            dayMaxEvents: 0,
            fixedWeekCount: false,
            dateClick: function (info) {
                info.jsEvent.preventDefault();
                sincronizandoDesdeMini = true;
                cal.gotoDate(info.dateStr);
                document.querySelectorAll('#miniCalendar .fc-daygrid-day.mini-seleccionado').forEach(function (elx) {
                    elx.classList.remove('mini-seleccionado');
                });
                info.dayEl.classList.add('mini-seleccionado');
            }
        });
        if (document.getElementById('miniCalendar')) miniCal.render();

        cal.on('datesSet', function (info) {
            if (sincronizandoDesdeMini) { sincronizandoDesdeMini = false; return; }
            if (document.getElementById('miniCalendar')) miniCal.gotoDate(info.view.currentStart);
        });

        const esMedico = @json(auth()->user()->isMedico());
        const citaEstadoUrl = '{{ url('citas') }}';

                window.abrirModalCita = function (event) {
            const p = event.extendedProps;
            document.getElementById('cmTitulo').textContent = event.title;

            if (p.esBloqueo) {
                document.getElementById('cmEstado').innerHTML = '<span class="pill gray">No disponible</span>';
                let datosB = '';
                if (p.hora) datosB += '<div><i class="fa-regular fa-clock"></i> ' + p.hora + (p.horaFin ? ' – ' + p.horaFin : '') + '</div>';
                if (p.medico) datosB += '<div><i class="fa-solid fa-user-doctor"></i> ' + p.medico + '</div>';
                document.getElementById('cmDatos').innerHTML = datosB;
                document.getElementById('cmAcciones').innerHTML = '<a href="{{ route('bloqueos.index') }}" class="btn btn-light btn-sm"><i class="fa-solid fa-ban"></i> Administrar bloqueos</a>';
                document.getElementById('citaModalFondo').classList.add('abierto');
                return;
            }
            document.getElementById('cmEstado').innerHTML = '<span class="ev-' + p.estado + '" style="padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:700">' + (p.estadoLabel || '') + '</span>';

            let datos = '';
            if (p.hora) datos += '<div><i class="fa-regular fa-clock"></i> ' + p.hora + '</div>';
            if (p.especialidad) datos += '<div><i class="fa-solid fa-stethoscope"></i> ' + p.especialidad + '</div>';
            if (p.medico) datos += '<div><i class="fa-solid fa-user-doctor"></i> ' + p.medico + '</div>';
            if (p.telefono) datos += '<div><i class="fa-solid fa-phone"></i> ' + p.telefono + '</div>';
            if (p.motivo) datos += '<div><i class="fa-regular fa-note-sticky"></i> ' + p.motivo + '</div>';
            document.getElementById('cmDatos').innerHTML = datos;

            let acciones = '';
            if (esMedico) {
                if (['pendiente','confirmada'].includes(p.estado)) {
                    acciones += '<a href="{{ url('consultas/create') }}?paciente_id=' + p.pacienteId + '&cita_id=' + event.id + '" class="btn btn-primary btn-sm"><i class="fa-solid fa-stethoscope"></i> Atender</a>';
                }
                      } else {
                acciones += '<a href="' + citaEstadoUrl + '/' + event.id + '/edit" class="btn btn-light btn-sm"><i class="fa-solid fa-pen"></i> Editar</a>';
                if (p.telefono) {
                    acciones += '<a href="https://wa.me/' + p.telefono.replace(/\D/g,'') + '" target="_blank" class="btn btn-light btn-sm" style="color:#25d366"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>';
                }
                const fechaStr = event.start.getFullYear()+'-'+String(event.start.getMonth()+1).padStart(2,'0')+'-'+String(event.start.getDate()).padStart(2,'0');
                acciones += '<button type="button" class="btn btn-primary btn-sm" onclick="cerrarModalCita(); abrirNuevaCitaModal(&quot;'+fechaStr+'&quot;,&quot;'+p.hora+'&quot;,30)"><i class="fa-solid fa-plus"></i> Agregar cita aquí</button>';
            }

            document.getElementById('cmAcciones').innerHTML = acciones;

            document.getElementById('citaModalFondo').classList.add('abierto');
        };

        window.cerrarModalCita = function (e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('citaModalFondo').classList.remove('abierto');
        };

        function pintarChip(chk){
            const color = colorMedico(parseInt(chk.value));
            const chip = chk.closest('.chip-medico');
            if (chk.checked) {
                chip.style.borderColor = color;
                chip.style.background = color + '18';
            } else {
                chip.style.borderColor = '#e5e7eb';
                chip.style.background = 'transparent';
            }
        }

              window.marcarTodosMedicos = function (valor) {
            document.querySelectorAll('.chkMedico').forEach(function (chk) {
                chk.checked = valor;
                pintarChip(chk);
            });
            cal.refetchEvents();
            guardarSeleccionMedicos();
        };

               // Recordar qué médicos estaban marcados la última vez
        let medicosGuardados = null;
        try {
            const guardado = localStorage.getItem('agenda_medicos_seleccionados');
            if (guardado) medicosGuardados = JSON.parse(guardado);
        } catch (e) {}

        document.querySelectorAll('.chkMedico').forEach(function (chk) {
            if (medicosGuardados !== null) {
                chk.checked = medicosGuardados.includes(chk.value);
            }
            const color = colorMedico(parseInt(chk.value));
            const dot = document.querySelector('.chip-dot[data-medico="'+chk.value+'"]');
            if (dot) { dot.style.background = color; dot.style.borderColor = color; }
            pintarChip(chk);
            chk.addEventListener('change', function () {
                pintarChip(chk);
                cal.refetchEvents();
                guardarSeleccionMedicos();
            });
        });

        function guardarSeleccionMedicos(){
            const seleccionados = Array.from(document.querySelectorAll('.chkMedico:checked')).map(c => c.value);
            try { localStorage.setItem('agenda_medicos_seleccionados', JSON.stringify(seleccionados)); } catch (e) {}
        }

        if (medicosGuardados !== null) {
            cal.refetchEvents();
        }
        document.getElementById('saltarFecha').addEventListener('change', function () {
            if (this.value) cal.gotoDate(this.value);
        });

        const pacientesLista = {!! json_encode($pacientes->map(function($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre_completo,
                'documento' => $p->documento,
            ];
        })->values()->toArray()) !!};

        window.abrirNuevaCitaModal = function (fecha, hora, duracion) {
            document.getElementById('ncFecha').value = fecha;
            document.getElementById('ncHora').value = hora;
            document.getElementById('formNuevaCitaRapida').dataset.duracion = duracion;
            document.getElementById('ncBuscarPaciente').value = '';
            document.getElementById('ncPacienteId').value = '';
                        document.getElementById('ncMedico').value = '';
            document.getElementById('ncConsultorio').value = '';
            document.getElementById('ncError').style.display = 'none';
            document.getElementById('ncMasOpciones').href = '{{ route('citas.create') }}?fecha=' + fecha + '&hora=' + hora + '&duracion=' + duracion;
            document.getElementById('nuevaCitaModalFondo').classList.add('abierto');
        };

        window.cerrarNuevaCitaModal = function (e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('nuevaCitaModalFondo').classList.remove('abierto');
        };

        const ncInput = document.getElementById('ncBuscarPaciente');
        const ncLista = document.getElementById('ncPacienteLista');
        ncInput.addEventListener('input', function () {
            document.getElementById('ncPacienteId').value = '';
            const q = ncInput.value.trim().toLowerCase();
            ncLista.innerHTML = '';
            if (!q) { ncLista.style.display = 'none'; return; }
            const encontrados = pacientesLista.filter(function (p) {
                return p.nombre.toLowerCase().includes(q) || (p.documento || '').toLowerCase().includes(q);
            }).slice(0, 20);
            if (!encontrados.length) { ncLista.style.display = 'none'; return; }
            encontrados.forEach(function (p) {
                const row = document.createElement('div');
                row.textContent = p.nombre + (p.documento ? ' — ' + p.documento : '');
                row.addEventListener('click', function () {
                    ncInput.value = p.nombre;
                    document.getElementById('ncPacienteId').value = p.id;
                    ncLista.style.display = 'none';
                });
                ncLista.appendChild(row);
            });
            ncLista.style.display = 'block';
        });
        document.addEventListener('click', function (e) {
            if (e.target !== ncInput) ncLista.style.display = 'none';
        });

        document.getElementById('formNuevaCitaRapida').addEventListener('submit', function (e) {
            e.preventDefault();
            const errorEl = document.getElementById('ncError');
            errorEl.style.display = 'none';

            const pacienteId = document.getElementById('ncPacienteId').value;
            if (!pacienteId) {
                errorEl.textContent = 'Busca y selecciona un paciente de la lista.';
                errorEl.style.display = 'block';
                return;
            }

            fetch('{{ route('citas.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    paciente_id: pacienteId,
                    fecha: document.getElementById('ncFecha').value,
                    hora: document.getElementById('ncHora').value,
                    duracion: this.dataset.duracion || 30,
                                        medico_id: document.getElementById('ncMedico').value || null,
                    consultorio_id: document.getElementById('ncConsultorio').value || null,
                    estado: 'pendiente'
                })
            })
            .then(async function (r) {
                const data = await r.json().catch(function () { return {}; });
                if (!r.ok) throw new Error(data.mensaje || 'No se pudo guardar la cita.');
                return data;
            })
            .then(function () {
                cerrarNuevaCitaModal();
                cal.refetchEvents();
            })
            .catch(function (err) {
                errorEl.textContent = err.message;
                errorEl.style.display = 'block';
            });
        });

        cal.render();
    });
    </script>
    @endpush
@endsection