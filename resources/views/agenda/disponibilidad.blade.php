@extends('layouts.app')
@section('title', 'Disponibilidad')

@section('content')
    <div class="page-head">
        <div><h1>Disponibilidad</h1><p>Mapa de médicos y consultorios ocupados/libres por horario.</p></div>
        <form method="GET" class="flex gap">
            <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()">
            <a href="{{ route('agenda.index') }}" class="btn btn-ghost"><i class="fa-solid fa-calendar-days"></i> Ver calendario</a>
        </form>
    </div>

    <h3 class="mb">Médicos</h3>
    <div class="card mb" style="overflow-x:auto;padding:0">
        <table class="disp-table">
            <thead>
                <tr>
                    <th class="disp-medico-col">Médico</th>
                    @foreach($slots as $s)
                        <th class="disp-hora {{ substr($s,3,2) === '00' ? 'disp-hora-en-punto' : '' }}">
                            {{ substr($s,3,2) === '00' ? \Carbon\Carbon::parse($s)->format('g A') : '' }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $m)
                    <tr>
                        <td class="disp-medico-col">
                            <span class="disp-dot" data-medico="{{ $m->id }}"></span> {{ $m->name }}
                        </td>
                        @foreach($slots as $s)
                            @php($ocupadoPor = $ocupado[$m->id][$s] ?? null)
                            <td class="disp-celda {{ $ocupadoPor ? 'disp-ocupado' : 'disp-libre' }}"
                                data-medico="{{ $ocupadoPor ? $m->id : '' }}"
                                title="{{ $ocupadoPor ? $s.' · '.$ocupadoPor : $s.' · Libre' }}">
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="100"><div class="empty"><i class="fa-solid fa-user-doctor"></i><p>No hay médicos registrados.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="ag-legend mt mb">
        <span><i style="background:#e2e8f0"></i> Libre</span>
        <span><i style="background:#0d9488"></i> Ocupado</span>
    </div>

    <h3 class="mb">Consultorios</h3>
    <div class="card" style="overflow-x:auto;padding:0">
        <table class="disp-table">
            <thead>
                <tr>
                    <th class="disp-medico-col">Consultorio</th>
                    @foreach($slots as $s)
                        <th class="disp-hora {{ substr($s,3,2) === '00' ? 'disp-hora-en-punto' : '' }}">
                            {{ substr($s,3,2) === '00' ? \Carbon\Carbon::parse($s)->format('g A') : '' }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($consultorios as $co)
                    <tr>
                        <td class="disp-medico-col">
                            <i class="fa-solid fa-door-open" style="color:var(--violet-2);margin-right:6px"></i>{{ $co->nombre }}
                        </td>
                                                               @foreach($slots as $s)
                            @php($ocupadoPor = $ocupadoConsultorio[$co->id][$s] ?? null)
                            <td class="disp-celda {{ $ocupadoPor ? 'disp-ocupado' : 'disp-libre' }}"
                                data-medico="{{ $ocupadoPor['medicoId'] ?? '' }}"
                                title="{{ $ocupadoPor ? $s.' · '.$ocupadoPor['texto'] : $s.' · Libre' }}">
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="100"><div class="empty"><i class="fa-solid fa-door-open"></i><p>No hay consultorios registrados. Agrégalos en Admin → Consultorios.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
    .disp-table{border-collapse:collapse;width:100%;font-size:12px}
        .disp-medico-col{position:sticky;left:0;background:#fff;z-index:2;text-align:left;
        padding:10px 16px;font-weight:600;white-space:nowrap;border-right:2px solid var(--line);
        width:180px;min-width:180px;max-width:180px;overflow:hidden;text-overflow:ellipsis}
    .disp-table thead th{background:var(--bg-pink);padding:6px 2px;font-size:10.5px;color:var(--ink-soft);
        font-weight:700;text-align:center;border-bottom:1px solid var(--line)}
    .disp-hora-en-punto{border-left:1px solid var(--line)}
    .disp-celda{width:22px;height:38px;border-right:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;
        cursor:default;transition:.1s}
    .disp-libre{background:#f8fafc}
    .disp-ocupado{opacity:.9}
    .disp-ocupado-consultorio{background:#0d9488}
    .disp-celda:hover{outline:2px solid #1f2937;outline-offset:-2px;position:relative;z-index:1}
    .disp-dot{width:9px;height:9px;border-radius:50%;display:inline-block;margin-right:4px}
    </style>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const paleta = ['#0d9488','#1e3a8a','#ca8a04','#db2777','#9333ea','#65a30d','#dc2626','#0891b2'];
    function colorMedico(id){ return paleta[id % paleta.length]; }

        document.querySelectorAll('.disp-dot').forEach(function (dot) {
            dot.style.background = colorMedico(parseInt(dot.dataset.medico));
        });
        document.querySelectorAll('.disp-ocupado').forEach(function (celda) {
            celda.style.background = colorMedico(parseInt(celda.dataset.medico));
        });
    });
    </script>
    @endpush
@endsection
