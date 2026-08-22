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

            // Construir el evento sin la propiedad 'url' por defecto
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

    public function mover(Request $request, Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        abort_if(auth()->user()->isMedico(), 403, 'El médico no puede reprogramar citas.');

        // Validación de choque de horario
        if ($cita->medico_id) {
            $fechaNueva = $request->fecha;
            $horaNueva = $request->hora ?? $cita->hora;
            
            $choque = Cita::where('empresa_id', $this->empresaId())
                ->where('medico_id', $cita->medico_id)
                ->where('id', '!=', $cita->id)
                ->whereDate('fecha', $fechaNueva)
                ->whereNotIn('estado', ['cancelada', 'no_asistio'])
                ->get()
                ->contains(function ($c) use ($fechaNueva, $horaNueva, $cita) {
                    $inicio = \Carbon\Carbon::parse($fechaNueva.' '.$horaNueva);
                    $fin = $inicio->copy()->addMinutes($cita->duracion ?: 30);
                    $cInicio = \Carbon\Carbon::parse($c->fecha->format('Y-m-d').' '.$c->hora);
                    $cFin = $cInicio->copy()->addMinutes($c->duracion ?: 30);
                    return $inicio < $cFin && $cInicio < $fin;
                });

            if ($choque) {
                return response()->json(['ok' => false, 'mensaje' => 'El médico ya tiene otra cita en ese horario.'], 422);
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