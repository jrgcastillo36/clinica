@extends('layouts.app')
@section('title', 'Agenda')

@section('content')
    <div class="page-head">
        @unless(auth()->user()->isMedico())
        <div class="filtro-medicos-wrap">
            <button type="button" id="btnFiltroMedicos" class="btn btn-light">
                <i class="fa-solid fa-user-doctor"></i> <span id="filtroMedicosLabel">Todos los médicos</span> <i class="fa-solid fa-chevron-down" style="font-size:10px"></i>
            </button>
            <div id="panelFiltroMedicos" class="filtro-medicos-panel">
                <div class="filtro-medicos-acciones">
                    <button type="button" onclick="marcarTodosMedicos(true)">Todos</button>
                    <button type="button" onclick="marcarTodosMedicos(false)">Ninguno</button>
                </div>
                @foreach($medicos as $m)
                    <label class="chip-medico">
                        <input type="checkbox" class="chkMedico" value="{{ $m->id }}" checked>
                        <span class="chip-dot" data-medico="{{ $m->id }}"></span>
                        {{ $m->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endunless
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
    .ag-stats{gap:14px}
    .ag-stat{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px 18px;box-shadow:0 4px 14px rgba(90,70,160,.05)}
    .ag-ic{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;font-size:19px;flex:0 0 46px}
    .ag-num{font-size:24px;font-weight:700;color:var(--ink);line-height:1}
    .ag-cap{font-size:12px;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.4px;margin-top:3px}
    .ag-legend{display:flex;gap:16px;flex-wrap:wrap;font-size:12.5px;color:var(--ink-soft);font-weight:500}
    .ag-legend span{display:flex;align-items:center;gap:6px}
    .ag-legend i{width:12px;height:12px;border-radius:4px;display:inline-block}
    .ag-card{padding:18px 18px 8px}
    .ag-card,.fc-scroller{scrollbar-gutter:stable}
    /* ---- FullCalendar tematizado ---- */
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
    /* Encabezados de día: cada día con su color */
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

    /* Número de día como chip notorio */
    .fc .fc-daygrid-day-top{flex-direction:row;justify-content:flex-end}
    .fc .fc-daygrid-day-number{font-size:13px;font-weight:800;margin:6px;padding:3px 9px;border-radius:9px;min-width:26px;text-align:center;color:#5b4b86;background:#f2eefb;line-height:1.2}
    .fc .fc-day-sun  .fc-daygrid-day-number{color:#dc2626;background:#fde3e3}
    .fc .fc-day-mon  .fc-daygrid-day-number{color:#6d28d9;background:#ece7fd}
    .fc .fc-day-tue  .fc-daygrid-day-number{color:#1d4ed8;background:#dbeafe}
    .fc .fc-day-wed  .fc-daygrid-day-number{color:#0f766e;background:#cdf5ec}
    .fc .fc-day-thu  .fc-daygrid-day-number{color:#b45309;background:#fdecc8}
    .fc .fc-day-fri  .fc-daygrid-day-number{color:#be185d;background:#fbdcec}
    .fc .fc-day-sat  .fc-daygrid-day-number{color:#15803d;background:#d6f5df}

    /* Tinte muy suave de columna para seguir cada día */
    .fc .fc-daygrid-day.fc-day-sun{background:#fffbfb}
    .fc .fc-daygrid-day.fc-day-sat{background:#fbfefc}

    /* HOY prevalece sobre los estilos por día */
    .fc .fc-daygrid-day.fc-day-today{background:#faf5ff!important}
    .fc .fc-day-today .fc-daygrid-day-number{background:linear-gradient(135deg,var(--violet-2),var(--pink))!important;color:#fff!important;border-radius:50%;width:26px;height:26px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin:6px 8px 6px 6px;box-shadow:0 3px 8px rgba(168,85,247,.4)}
    .fc .fc-daygrid-day-frame{min-height:96px}

    /* Eventos como chips suaves */
    .fc .fc-daygrid-event,.fc .fc-timegrid-event{background:transparent!important;border:none!important;box-shadow:none!important;margin:2px 4px!important}
    .fc .fc-daygrid-event-harness{margin-top:1px}
    .ev{display:flex;align-items:center;gap:6px;padding:4px 8px;border-radius:8px;font-size:11.5px;font-weight:600;overflow:hidden;white-space:nowrap;transition:.12s;background:#f1f5f9;color:#1f2937}
    .ev:hover{transform:translateX(2px)}
    .ev .ev-dot{width:7px;height:7px;border-radius:50%;flex:0 0 7px}
    .ev .ev-time{font-weight:700;opacity:.9}
    .ev .ev-title{overflow:hidden;text-overflow:ellipsis}
    .ev-pendiente{background:#fef3c7;color:#92400e}
    .ev-confirmada{background:#dbeafe;color:#1e40af}
    .ev-atendida{background:#dcfce7;color:#166534}
    .ev-cancelada{background:#fee2e2;color:#991b1b}
    .ev-no_asistio{background:#f1f5f9;color:#475569}
    .fc .fc-timegrid-event .ev{white-space:normal}
    .fc .fc-more-link{color:var(--violet);font-weight:600;font-size:11px}

    .ev-med-dot{width:7px;height:7px;border-radius:50%;flex:0 0 7px;margin-left:auto}
    .ev-dia{flex-direction:column;align-items:flex-start;gap:3px;white-space:normal;padding:8px 10px}
    .ev-dia-top{display:flex;align-items:center;gap:6px;font-size:12.5px}
    .ev-linea{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:500;opacity:.9}
    .ev-linea i{width:12px;font-size:10px}

    .cita-tooltip{position:absolute;z-index:9999;background:#1f2937;color:#fff;padding:10px 14px;
        border-radius:10px;font-size:12.5px;line-height:1.7;box-shadow:0 10px 30px rgba(0,0,0,.25);
        max-width:240px;pointer-events:none}
    .cita-tooltip b{font-size:13.5px;display:block;margin-bottom:4px}
    .cita-tooltip i{width:14px;opacity:.75;margin-right:4px}
    .cita-tooltip .tt-estado{margin-top:6px;font-weight:700;text-transform:uppercase;font-size:10.5px;letter-spacing:.4px;opacity:.85}

    .filtro-medicos-wrap{position:relative}
    .filtro-medicos-panel{display:none;position:absolute;top:calc(100% + 6px);left:0;z-index:60;
        background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow-lg);
        padding:10px 14px;min-width:230px;max-height:280px;overflow-y:auto}
    .filtro-medicos-panel.abierto{display:block}
    .filtro-medicos-acciones{display:flex;gap:8px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid var(--line)}
    .filtro-medicos-acciones button{background:var(--bg-pink);border:none;border-radius:8px;
        padding:5px 12px;font-size:11px;font-weight:600;cursor:pointer;color:var(--violet)}

    .chip-medico{display:flex!important;align-items:center;gap:8px;padding:8px 10px;border-radius:10px;
        font-size:13px;font-weight:600;cursor:pointer;margin-bottom:4px;
        border:1.5px solid #e5e7eb;color:#9ca3af;transition:.15s}
    .chip-medico:has(.chkMedico:checked){color:#1f2937}
    .chip-medico input[type=checkbox]{display:none}
    .chip-dot{width:11px;height:11px;border-radius:50%;flex:0 0 11px;border:2px solid #e5e7eb}

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

    /* ---- Vistas Semana y Día (timeGrid) ---- */
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
    /* En Semana/Día el número del encabezado se ve más grande */
    .fc .fc-timeGridWeek-view .fc-col-header-cell-cushion,
    .fc .fc-timeGridDay-view .fc-col-header-cell-cushion{font-size:12.5px;line-height:1.4}
    [data-theme="dark"] .ag-stat,[data-theme="dark"] .fc{background:#161428}
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
            tooltipEl.innerHTML =
                '<b>' + event.title + '</b>' +
                (p.hora ? '<div><i class="fa-regular fa-clock"></i> ' + p.hora + '</div>' : '') +
                (p.especialidad ? '<div><i class="fa-solid fa-stethoscope"></i> ' + p.especialidad + '</div>' : '') +
                (p.medico ? '<div><i class="fa-solid fa-user-doctor"></i> ' + p.medico + '</div>' : '') +
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

        const cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'es',
            height: 760,
            expandRows: true,
            dayMaxEvents: 3,
            fixedWeekCount: false,
            headerToolbar: { left:'prev,next today saltarFecha', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
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
            buttonText: { today:'Hoy', month:'Mes', week:'Semana', day:'Día' },

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
                const esMedicoLogueado = @json(auth()->user()->isMedico());
                const estadoDot = '<span class="ev-dot" style="background:'+(arg.event.backgroundColor||'#7c3aed')+'" title="'+(p.estadoLabel||'')+'"></span>';

                if (arg.view.type === 'timeGridDay') {
                    const rango = p.hora + (p.horaFin ? ' – ' + p.horaFin : '');
                    const medicoLinea = (!esMedicoLogueado && p.medico) ? '<div class="ev-linea"><i class="fa-solid fa-user-doctor"></i> '+p.medico+'</div>' : '';
                    return { html:
                        '<div class="ev ev-dia">'+
                            '<div class="ev-dia-top">'+estadoDot+'<b>'+rango+'</b></div>'+
                            '<div class="ev-linea"><i class="fa-solid fa-user"></i> '+arg.event.title+'</div>'+
                            medicoLinea+
                        '</div>' };
                }

                const time = p.hora ? '<span class="ev-time">'+p.hora+'</span>' : '';
                const textoPrincipal = esMedicoLogueado ? arg.event.title : (p.medico || arg.event.title);
                const title = '<span class="ev-title">'+textoPrincipal+'</span>';
                return { html: '<div class="ev">'+estadoDot+time+title+'</div>' };
            },

            eventDidMount: function(arg){
                const p = arg.event.extendedProps;
                const evEl = arg.el.querySelector('.ev');
                if (p.medicoId && evEl) {
                    evEl.style.borderLeft = '4px solid ' + colorMedico(p.medicoId);
                    evEl.style.background = colorMedicoBg(p.medicoId);
                }
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
                window.location = '{{ route('citas.create') }}?fecha=' + fecha + '&hora=' + hora + '&duracion=' + duracion;
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
            }
        });

        const esMedico = @json(auth()->user()->isMedico());
        const citaEstadoUrl = '{{ url('citas') }}';

        window.abrirModalCita = function (event) {
            const p = event.extendedProps;
            document.getElementById('cmTitulo').textContent = event.title;
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
            }
            document.getElementById('cmAcciones').innerHTML = acciones;

            document.getElementById('citaModalFondo').classList.add('abierto');
        };

        window.cerrarModalCita = function (e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('citaModalFondo').classList.remove('abierto');
        };

        function actualizarLabelMedicos(){
            const total = document.querySelectorAll('.chkMedico').length;
            const marcados = document.querySelectorAll('.chkMedico:checked').length;
            const label = document.getElementById('filtroMedicosLabel');
            if (!label) return;
            if (marcados === total) label.textContent = 'Todos los médicos';
            else if (marcados === 0) label.textContent = 'Ningún médico';
            else label.textContent = marcados + ' de ' + total + ' médicos';
        }

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
            actualizarLabelMedicos();
            cal.refetchEvents();
        };

        document.querySelectorAll('.chkMedico').forEach(function (chk) {
            const color = colorMedico(parseInt(chk.value));
            const dot = document.querySelector('.chip-dot[data-medico="'+chk.value+'"]');
            if (dot) { dot.style.background = color; dot.style.borderColor = color; }
            pintarChip(chk);
            chk.addEventListener('change', function () { pintarChip(chk); actualizarLabelMedicos(); cal.refetchEvents(); });
        });

        const btnFiltro = document.getElementById('btnFiltroMedicos');
        const panelFiltro = document.getElementById('panelFiltroMedicos');
        if (btnFiltro) {
            btnFiltro.addEventListener('click', function (e) {
                e.stopPropagation();
                panelFiltro.classList.toggle('abierto');
            });
            document.addEventListener('click', function (e) {
                if (!panelFiltro.contains(e.target) && e.target !== btnFiltro) {
                    panelFiltro.classList.remove('abierto');
                }
            });
        }
        document.getElementById('saltarFecha').addEventListener('change', function () {
            if (this.value) cal.gotoDate(this.value);
        });
        cal.render();
    });
    </script>
    @endpush
@endsection