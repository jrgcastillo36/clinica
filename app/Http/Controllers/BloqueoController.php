<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BloqueoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $medicos = User::where('empresa_id', $this->empresaId())->where('role', 'medico')->orderBy('name')->get();

        $bloqueos = Cita::where('empresa_id', $this->empresaId())
            ->where('es_bloqueo', true)
            ->where('fecha', '>=', now()->toDateString())
            ->with('medico')
            ->orderBy('fecha')
            ->get()
            ->groupBy('bloqueo_grupo')
            ->map(function ($grupo) {
                $primero = $grupo->first();
                return [
                    'grupo' => $primero->bloqueo_grupo,
                    'medico' => $primero->medico->name ?? '—',
                    'motivo' => $primero->motivo,
                    'desde' => $grupo->min('fecha'),
                    'hasta' => $grupo->max('fecha'),
                    'hora_inicio' => substr((string) $primero->hora, 0, 5),
                    'hora_fin' => \Carbon\Carbon::parse($primero->hora)->addMinutes($primero->duracion)->format('H:i'),
                ];
            })
            ->values();

        return view('bloqueos.index', compact('medicos', 'bloqueos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medico_id' => ['required', 'exists:users,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'hora_inicio' => ['nullable'],
            'hora_fin' => ['nullable'],
            'motivo' => ['required', 'string', 'max:150'],
        ]);

        $horaInicio = $data['hora_inicio'] ?: '00:00';
        $horaFin = $data['hora_fin'] ?: '23:59';
        $duracionMin = \Carbon\Carbon::parse($horaInicio)->diffInMinutes(\Carbon\Carbon::parse($horaFin));

        $grupo = (string) Str::uuid();

        $cursor = \Carbon\Carbon::parse($data['fecha_inicio']);
        $fin = \Carbon\Carbon::parse($data['fecha_fin']);

        while ($cursor->lte($fin)) {
            Cita::create([
                'empresa_id' => $this->empresaId(),
                'paciente_id' => null,
                'medico_id' => $data['medico_id'],
                'fecha' => $cursor->toDateString(),
                'hora' => $horaInicio,
                'duracion' => max($duracionMin, 5),
                'estado' => 'confirmada',
                'motivo' => $data['motivo'],
                'es_bloqueo' => true,
                'bloqueo_grupo' => $grupo,
            ]);
            $cursor->addDay();
        }

        return back()->with('ok', 'Horario bloqueado correctamente.');
    }

    public function destroy(string $grupo)
    {
        Cita::where('empresa_id', $this->empresaId())
            ->where('bloqueo_grupo', $grupo)
            ->where('es_bloqueo', true)
            ->delete();

        return back()->with('ok', 'Bloqueo eliminado.');
    }
}
