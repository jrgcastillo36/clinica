<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $medicos = auth()->user()->isMedico()
            ? collect()
            : User::where('empresa_id', $this->empresaId())
                ->where('role', 'medico')
                ->orderBy('name')
                ->get();

        return view('agenda.index', compact('medicos'));
    }

    public function eventos(Request $request)
    {
        $colores = [
            'pendiente' => '#f59e0b',
            'confirmada' => '#3b82f6',
            'atendida' => '#22c55e',
            'cancelada' => '#ef4444',
            'no_asistio' => '#94a3b8',
        ];

        $etiquetas = [
            'pendiente' => 'Pendiente',
            'confirmada' => 'Confirmada',
            'atendida' => 'Atendida',
            'cancelada' => 'Cancelada',
            'no_asistio' => 'No asistió',
        ];

        $query = Cita::with(['paciente', 'especialidad', 'medico'])
            ->where('empresa_id', $this->empresaId());

        // Filtro por médico
        if (auth()->user()->isMedico()) {
            $query->where('medico_id', auth()->id());
        } elseif ($request->filled('medico_ids')) {
            $ids = array_filter(explode(',', $request->medico_ids));
            if ($ids) $query->whereIn('medico_id', $ids);
        }

        // Filtro por fechas
        if ($request->start) {
            $query->whereDate('fecha', '>=', substr($request->start, 0, 10));
        }
        if ($request->end) {
            $query->whereDate('fecha', '<=', substr($request->end, 0, 10));
        }

        $citas = $query->get();

        $eventos = $citas->map(function ($c) use ($colores, $etiquetas) {
            $hora = substr((string) $c->hora, 0, 5);
            $inicio = $c->fecha->format('Y-m-d').'T'.$hora.':00';
            $color = $colores[$c->estado] ?? '#7c3aed';

            $evento = [
                'id' => $c->id,
                'title' => $c->paciente->nombre_completo,
                'start' => $inicio,
                'color' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'estado' => $c->estado,
                    'estadoLabel' => $etiquetas[$c->estado] ?? ucfirst($c->estado),
                    'hora' => $hora,
                    'especialidad' => $c->especialidad->nombre ?? 'General',
                    'medico' => $c->medico->name ?? null,
                    'medicoId' => $c->medico_id,
                    'motivo' => $c->motivo,
                    'telefono' => $c->paciente->telefono ?? null,
                    'pacienteId' => $c->paciente_id,
                    'horaFin' => \Carbon\Carbon::parse($hora)->addMinutes($c->duracion ?: 30)->format('H:i'),
                ],
            ];

            return $evento;
        });

        return response()->json($eventos);
    }

    public function disponibilidad(Request $request)
    {
        $fecha = $request->get('fecha', now()->toDateString());

        $medicos = User::where('empresa_id', $this->empresaId())
            ->where('role', 'medico')->orderBy('name')->get();

        $consultorios = \App\Models\Consultorio::where('empresa_id', $this->empresaId())
            ->where('activo', true)->orderBy('nombre')->get();

        $citas = Cita::where('empresa_id', $this->empresaId())
            ->whereDate('fecha', $fecha)
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->get();

        $slots = [];
        $cursor = \Carbon\Carbon::parse('07:00');
        $fin = \Carbon\Carbon::parse('21:00');
        while ($cursor < $fin) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes(30);
        }

        $ocupado = [];
        foreach ($medicos as $m) {
            foreach ($slots as $s) {
                $ocupado[$m->id][$s] = null;
            }
        }

        $ocupadoConsultorio = [];
        foreach ($consultorios as $co) {
            foreach ($slots as $s) {
                $ocupadoConsultorio[$co->id][$s] = null;
            }
        }

        foreach ($citas as $c) {
            $inicio = \Carbon\Carbon::parse(substr($c->hora, 0, 5));
            $finCita = $inicio->copy()->addMinutes($c->duracion ?: 30);

            foreach ($slots as $s) {
                $slotTime = \Carbon\Carbon::parse($s);
                if ($slotTime->gte($inicio) && $slotTime->lt($finCita)) {
                    if ($c->medico_id) {
                        $ocupado[$c->medico_id][$s] = $c->paciente->nombre_completo ?? 'Ocupado';
                    }
                    if ($c->consultorio_id) {
                        $ocupadoConsultorio[$c->consultorio_id][$s] = [
                            'texto' => $c->paciente->nombre_completo ?? 'Ocupado',
                            'medicoId' => $c->medico_id,
                        ];
                    }
                }
            }
        }

        return view('agenda.disponibilidad', compact('medicos', 'slots', 'ocupado', 'fecha', 'consultorios', 'ocupadoConsultorio'));
    }

    public function mover(Request $request, Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        abort_if(auth()->user()->isMedico(), 403, 'El médico no puede reprogramar citas.');

        // Validación de choque de horario (médico y/o consultorio)
        if ($cita->medico_id || $cita->consultorio_id) {
            $fechaNueva = $request->fecha;
            $horaNueva = $request->hora ?? $cita->hora;
            $inicio = \Carbon\Carbon::parse($fechaNueva.' '.$horaNueva);
            $fin = $inicio->copy()->addMinutes($cita->duracion ?: 30);

            $candidatas = Cita::where('empresa_id', $this->empresaId())
                ->where('id', '!=', $cita->id)
                ->where(function ($q) use ($cita) {
                    if ($cita->medico_id) $q->orWhere('medico_id', $cita->medico_id);
                    if ($cita->consultorio_id) $q->orWhere('consultorio_id', $cita->consultorio_id);
                })
                ->whereDate('fecha', $fechaNueva)
                ->whereNotIn('estado', ['cancelada', 'no_asistio'])
                ->get();

            foreach ($candidatas as $c) {
                $cInicio = \Carbon\Carbon::parse($c->fecha->format('Y-m-d').' '.$c->hora);
                $cFin = $cInicio->copy()->addMinutes($c->duracion ?: 30);
                if ($inicio < $cFin && $cInicio < $fin) {
                    if ($cita->medico_id && $c->medico_id == $cita->medico_id) {
                        return response()->json(['ok' => false, 'mensaje' => 'El médico ya tiene otra cita en ese horario.'], 422);
                    }
                    if ($cita->consultorio_id && $c->consultorio_id == $cita->consultorio_id) {
                        return response()->json(['ok' => false, 'mensaje' => 'Ese consultorio ya está ocupado en ese horario.'], 422);
                    }
                }
            }
        }

        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'hora' => ['nullable'],
        ]);

        $cita->update([
            'fecha' => $data['fecha'],
            'hora' => $data['hora'] ?? $cita->hora,
        ]);

        return response()->json(['ok' => true]);
    }
}