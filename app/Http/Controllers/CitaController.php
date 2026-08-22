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

    // ============================================================
    // NUEVO MÉTODO PARA VALIDAR CHOQUES DE HORARIO
    // ============================================================
    private function hayChoque(?int $medicoId, string $fecha, string $hora, ?int $duracion, ?int $ignorarId = null): bool
    {
        if (! $medicoId) return false;

        $inicio = \Carbon\Carbon::parse($fecha.' '.$hora);
        $fin = $inicio->copy()->addMinutes($duracion ?: 30);

        return Cita::where('empresa_id', $this->empresaId())
            ->where('medico_id', $medicoId)
            ->whereDate('fecha', $fecha)
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->get()
            ->contains(function ($c) use ($inicio, $fin) {
                $cInicio = \Carbon\Carbon::parse($c->fecha->format('Y-m-d').' '.$c->hora);
                $cFin = $cInicio->copy()->addMinutes($c->duracion ?: 30);
                return $inicio < $cFin && $cInicio < $fin;
            });
    }

    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $citas = Cita::where('empresa_id', $this->empresaId())
                    ->when(auth()->user()->isMedico(), fn ($q) => $q->where('medico_id', auth()->id()))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->with(['paciente', 'medico', 'especialidad'])
            ->orderBy('fecha', 'desc')->orderBy('hora')
            ->paginate(12)->withQueryString();

        return view('citas.index', compact('citas', 'estado'));
    }

       public function create(Request $request)
    {
        return view('citas.form', [
            'cita' => new Cita([
                'fecha' => $request->get('fecha', now()->toDateString()),
                'hora' => '09:00',
            ]),
        ] + $this->opciones());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // VALIDACIÓN DE CHOQUE DE HORARIO
        if ($this->hayChoque($data['medico_id'] ?? null, $data['fecha'], $data['hora'], $data['duracion'] ?? 30)) {
            return back()->withInput()->withErrors(['hora' => 'El médico ya tiene otra cita en ese horario.']);
        }

        $data['empresa_id'] = $this->empresaId();
        $cita = Cita::create($data);

        // Enviar confirmación por correo si el paciente tiene email
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
        if ($this->hayChoque($data['medico_id'] ?? null, $data['fecha'], $data['hora'], $data['duracion'] ?? 30, $cita->id)) {
            return back()->withInput()->withErrors(['hora' => 'El médico ya tiene otra cita en ese horario.']);
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
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:users,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
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