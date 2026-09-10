<?php

namespace App\Http\Controllers;

use App\Mail\CitaMail;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CitaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    private function hayChoque(?int $medicoId, ?int $consultorioId, string $fecha, string $hora, ?int $duracion, ?int $ignorarId = null): ?string
    {
        if (! $medicoId && ! $consultorioId) return null;

        $inicio = \Carbon\Carbon::parse($fecha.' '.$hora);
        $fin = $inicio->copy()->addMinutes($duracion ?: 30);

        $citas = Cita::where('empresa_id', $this->empresaId())
            ->where(function ($q) use ($medicoId, $consultorioId) {
                if ($medicoId) $q->orWhere('medico_id', $medicoId);
                if ($consultorioId) $q->orWhere('consultorio_id', $consultorioId);
            })
            ->whereDate('fecha', $fecha)
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->get();

        foreach ($citas as $c) {
            $cInicio = \Carbon\Carbon::parse($c->fecha->format('Y-m-d').' '.$c->hora);
            $cFin = $cInicio->copy()->addMinutes($c->duracion ?: 30);
            if ($inicio < $cFin && $cInicio < $fin) {
                if ($medicoId && $c->medico_id == $medicoId) {
                    if ($c->es_bloqueo) return 'El médico no está disponible en ese horario ('.($c->motivo ?: 'bloqueado').').';
                    return 'El médico ya tiene otra cita en ese horario.';
                }
            if ($consultorioId && $c->consultorio_id == $consultorioId) return 'Ese consultorio ya está ocupado en ese horario.';
            }
        }

        return null;
    }
    private function medicoDisponible(?int $medicoId, string $fecha, string $hora): bool
    {
        if (! $medicoId) return true;
        $horarios = \App\Models\HorarioMedico::where('user_id', $medicoId)->where('activo', true)->get();
        if ($horarios->isEmpty()) return true; // sin horarios definidos => sin restricción
        $dow = (int) \Carbon\Carbon::parse($fecha)->dayOfWeek;
        foreach ($horarios->where('dia_semana', $dow) as $h) {
            $ini = substr($h->hora_inicio, 0, 5);
            $fin = substr($h->hora_fin, 0, 5);
            if ($hora >= $ini && $hora < $fin) return true;
        }
        return false;
    }


    public function index(Request $request)
{
    $estado = $request->get('estado');
    $desde = $request->get('desde');
    $hasta = $request->get('hasta');

    $citas = Cita::where('empresa_id', $this->empresaId())
        ->where('es_bloqueo', false)
        ->when(auth()->user()->isMedico(), fn ($q) => $q->where('medico_id', auth()->id()))
        ->when($estado, fn ($q) => $q->where('estado', $estado))
        ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
        ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta))
        ->with(['paciente', 'medico', 'especialidad'])
        ->orderBy('fecha', 'desc')->orderBy('hora')
        ->paginate(12)->withQueryString();

    return view('citas.index', compact('citas', 'estado', 'desde', 'hasta'));
}

    public function create(Request $request)
    {
        return view('citas.form', [
            'cita' => new Cita([
                'fecha' => $request->get('fecha', now()->toDateString()),
                'hora' => $request->get('hora', '09:00'),
                'duracion' => $request->get('duracion', 30),
            ]),
        ] + $this->opciones());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // VALIDACIÓN DE CHOQUE DE HORARIO (médico y/o consultorio)
               if ($choque = $this->hayChoque($data['medico_id'] ?? null, $data['consultorio_id'] ?? null, $data['fecha'], $data['hora'], $data['duracion'] ?? 30)) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'mensaje' => $choque], 422);
            }
            return back()->withInput()->withErrors(['hora' => $choque]);
        }

        if (($data['medico_id'] ?? null) && ! $this->medicoDisponible($data['medico_id'], $data['fecha'], $data['hora'])) {
            $mensajeHorario = 'El médico no atiende en ese horario. Elige otro.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'mensaje' => $mensajeHorario], 422);
            }
            return back()->withInput()->withErrors(['hora' => $mensajeHorario]);
        }

                $data['empresa_id'] = $this->empresaId();

               if (empty($data['especialidad_id'])) {
            $data['especialidad_id'] = \App\Models\Especialidad::where('slug', 'psicologia')->value('id');
        }

        $cita = Cita::create($data);

        $cita->load(['paciente', 'especialidad', 'medico', 'empresa']);
        if ($cita->paciente && $cita->paciente->email) {
            try {
                Mail::to($cita->paciente->email)->send(new CitaMail($cita, 'confirmacion'));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Notificacion::crear($cita->empresa_id, 'Nueva cita agendada', [
            'tipo' => 'cita', 'icono' => 'fa-calendar-plus',
            'mensaje' => $cita->paciente->nombre_completo.' · '.$cita->fecha->format('d/m/Y').' '.substr((string) $cita->hora, 0, 5),
            'url' => route('citas.index'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'mensaje' => 'Cita agendada correctamente.']);
        }

        return redirect()->route('citas.index')->with('ok', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        return view('citas.form', ['cita' => $cita] + $this->opciones());
    }

    public function update(Request $request, Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);

        $data = $this->validated($request);

        // VALIDACIÓN DE CHOQUE DE HORARIO (ignorando la cita actual)
             if ($choque = $this->hayChoque($data['medico_id'] ?? null, $data['consultorio_id'] ?? null, $data['fecha'], $data['hora'], $data['duracion'] ?? 30, $cita->id)) {
            return back()->withInput()->withErrors(['hora' => $choque]);
        }

        if (($data['medico_id'] ?? null) && ! $this->medicoDisponible($data['medico_id'], $data['fecha'], $data['hora'])) {
            return back()->withInput()->withErrors(['hora' => 'El médico no atiende en ese horario. Elige otro.']);
        }

        $cita->update($data);

        return redirect()->route('citas.index')->with('ok', 'Cita actualizada.');
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'estado' => ['required', 'in:pendiente,confirmada,atendida,cancelada,no_asistio'],
        ]);

        $cita->update(['estado' => $data['estado']]);

        return back()->with('ok', 'Estado de la cita actualizado.');
    }

    public function destroy(Cita $cita)
    {
        abort_unless($cita->empresa_id === $this->empresaId(), 403);
        $cita->delete();

        return redirect()->route('citas.index')->with('ok', 'Cita eliminada.');
    }

    private function opciones(): array
    {
        $empresa = auth()->user()->empresa;
        return [
            'pacientes' => Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get(),
            'medicos' => User::where('empresa_id', $this->empresaId())->where('role', 'medico')->get(),
            'especialidades' => $empresa?->especialidadesActivas()->get() ?? collect(),
            'consultorios' => \App\Models\Consultorio::where('empresa_id', $this->empresaId())->where('activo', true)->orderBy('nombre')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'consultorio_id' => ['nullable', 'exists:consultorios,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required'],
            'duracion' => ['nullable', 'integer', 'min:5', 'max:240'],
            'estado' => ['required', 'in:pendiente,confirmada,atendida,cancelada,no_asistio'],
            'motivo' => ['nullable', 'string', 'max:200'],
            'notas' => ['nullable', 'string'],
            'es_teleconsulta' => ['nullable', 'boolean'],
        ]);
    }
}