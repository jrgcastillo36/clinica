<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\HorarioMedico;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReservaController extends Controller
{
    private const DURACION = 90;

    private function paciente()
    {
        return Auth::guard('paciente')->user();
    }

    /**
     * Devuelve las horas de inicio disponibles para un médico en una fecha
     * específica, en bloques fijos de 90 minutos, respetando su horario
     * semanal (incluyendo tramos partidos, ej. mañana y tarde por separado)
     * y descartando cualquier hora que choque con una cita ya existente
     * (incluye bloqueos, ya que se guardan como citas normales).
     */
    private function franjasDisponibles(int $medicoId, string $fecha, int $empresaId, int $duracion = self::DURACION): array
    {
        $dow = (int) Carbon::parse($fecha)->dayOfWeek;

        $horarios = HorarioMedico::where('user_id', $medicoId)
            ->where('activo', true)
            ->where('dia_semana', $dow)
            ->get();

        if ($horarios->isEmpty()) {
            return [];
        }

        $slots = [];
        foreach ($horarios as $h) {
            [$hIni, $mIni] = array_map('intval', explode(':', substr($h->hora_inicio, 0, 5)));
            [$hFin, $mFin] = array_map('intval', explode(':', substr($h->hora_fin, 0, 5)));
            $actual = $hIni * 60 + $mIni;
            $limite = $hFin * 60 + $mFin;

            while ($actual + $duracion <= $limite) {
                $hora = sprintf('%02d:%02d', intdiv($actual, 60), $actual % 60);
                if ($this->horaLibre($medicoId, $fecha, $hora, $duracion, $empresaId)) {
                    $slots[] = $hora;
                }
                $actual += $duracion;
            }
        }

        return $slots;
    }

    /**
     * ¿Este médico está libre en este rango exacto de tiempo? Revisa contra
     * TODAS sus citas de ese día (incluye bloqueos, que se guardan igual).
     */
    private function horaLibre(int $medicoId, string $fecha, string $hora, int $duracion, int $empresaId, ?int $ignorarCitaId = null): bool
    {
        $inicio = Carbon::parse($fecha.' '.$hora);
        $fin = $inicio->copy()->addMinutes($duracion);

        $query = Cita::where('empresa_id', $empresaId)
            ->where('medico_id', $medicoId)
            ->whereDate('fecha', $fecha)
            ->whereNotIn('estado', ['cancelada', 'no_asistio']);

        if ($ignorarCitaId) {
            $query->whereKeyNot($ignorarCitaId);
        }

        $choque = $query->get()->contains(function ($c) use ($inicio, $fin) {
            $cInicio = Carbon::parse($c->fecha->format('Y-m-d').' '.$c->hora);
            $cFin = $cInicio->copy()->addMinutes($c->duracion ?: self::DURACION);
            return $inicio < $cFin && $cInicio < $fin;
        });

        return ! $choque;
    }

    public function create()
    {
        $p = $this->paciente();
        $empresa = $p->empresa;

        return view('portal.reservar', [
            'paciente' => $p,
            'empresa' => $empresa,
            'especialidades' => $empresa?->especialidadesActivas()->get() ?? collect(),
            'medicos' => User::where('empresa_id', $empresa->id)->where('role', 'medico')->where('activo', true)->get(),
        ]);
    }

    /**
     * Endpoint AJAX: dado un médico y una fecha, devuelve las horas
     * de inicio disponibles (bloques de 90 min) para mostrar como chips.
     */
    public function franjasAjax(Request $request)
    {
        $p = $this->paciente();
        $empresa = $p->empresa;

        $data = $request->validate([
            'medico_id' => ['required', 'exists:users,id'],
            'fecha' => ['required', 'date'],
        ]);

        $medico = User::where('empresa_id', $empresa->id)->where('role', 'medico')->findOrFail($data['medico_id']);

        return response()->json(
            $this->franjasDisponibles($medico->id, $data['fecha'], $empresa->id)
        );
    }

    public function store(Request $request)
    {
        $p = $this->paciente();
        $empresa = $p->empresa;

        $data = $request->validate([
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'medico_id' => ['required', 'exists:users,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'string'],
            'motivo' => ['nullable', 'string', 'max:200'],
        ]);

        // Vuelve a calcular las franjas disponibles en este momento exacto
        // (no confiar solo en lo que el paciente vio hace unos segundos) y
        // confirma que la hora elegida siga siendo una opción válida.
        $disponibles = $this->franjasDisponibles((int) $data['medico_id'], $data['fecha'], $empresa->id);

        if (! in_array($data['hora'], $disponibles, true)) {
            return back()->withErrors(['hora' => 'Ese horario ya no está disponible. Elige otro.'])->withInput();
        }

        $cita = Cita::create([
            'empresa_id' => $empresa->id,
            'paciente_id' => $p->id,
            'medico_id' => $data['medico_id'],
            'especialidad_id' => $data['especialidad_id'] ?? $p->especialidad_id,
            'fecha' => $data['fecha'],
            'hora' => $data['hora'].':00',
            'duracion' => self::DURACION,
            'estado' => 'pendiente',
            'motivo' => $data['motivo'] ?? 'Reserva online',
        ]);

        Notificacion::crear($empresa->id, 'Reserva online del paciente', [
            'tipo' => 'cita', 'icono' => 'fa-globe',
            'mensaje' => $p->nombre_completo.' reservó para el '.$cita->fecha->format('d/m/Y').' '.$data['hora'],
            'url' => route('citas.index'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Tu cita fue solicitada. La clínica la confirmará pronto.');
    }

    public function editar(Cita $cita)
    {
        $this->ownCita($cita);
        $empresa = $this->paciente()->empresa;

        return view('portal.cita-editar', [
            'cita' => $cita,
            'empresa' => $empresa,
            'medicos' => User::where('empresa_id', $empresa->id)->where('role', 'medico')->where('activo', true)->get(),
        ]);
    }

    public function franjasEditarAjax(Request $request, Cita $cita)
    {
        $this->ownCita($cita);

        $data = $request->validate([
            'fecha' => ['required', 'date'],
        ]);

        $slots = $this->franjasDisponibles($cita->medico_id, $data['fecha'], $cita->empresa_id);

        // Al reprogramar, la hora ORIGINAL de esta misma cita sigue siendo válida
        // aunque el chequeo normal la encontraría "ocupada" por sí misma.
        if ($cita->fecha->format('Y-m-d') === $data['fecha']) {
            $horaOriginal = substr($cita->hora, 0, 5);
            if (! in_array($horaOriginal, $slots, true)) {
                $slots[] = $horaOriginal;
                sort($slots);
            }
        }

        return response()->json($slots);
    }

    public function actualizar(Request $request, Cita $cita)
    {
        $this->ownCita($cita);
        $data = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'string'],
        ]);

        if (! $cita->medico_id) {
            $cita->update(['fecha' => $data['fecha'], 'hora' => $data['hora'].':00', 'estado' => 'pendiente']);
        } else {
            $libre = $this->horaLibre($cita->medico_id, $data['fecha'], $data['hora'], $cita->duracion ?: self::DURACION, $cita->empresa_id, $cita->id);

            if (! $libre) {
                return back()->withErrors(['hora' => 'Ese horario ya está ocupado.'])->withInput();
            }

            $cita->update(['fecha' => $data['fecha'], 'hora' => $data['hora'].':00', 'estado' => 'pendiente']);
        }

        Notificacion::crear($cita->empresa_id, 'Cita reprogramada por el paciente', [
            'tipo' => 'cita', 'icono' => 'fa-calendar-day',
            'mensaje' => $this->paciente()->nombre_completo.' movió su cita al '.$cita->fecha->format('d/m/Y').' '.$data['hora'],
            'url' => route('citas.index'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Tu cita fue reprogramada.');
    }

    public function cancelar(Cita $cita)
    {
        $this->ownCita($cita);
        $cita->update(['estado' => 'cancelada']);

        Notificacion::crear($cita->empresa_id, 'Cita cancelada por el paciente', [
            'tipo' => 'alerta', 'icono' => 'fa-calendar-xmark',
            'mensaje' => $this->paciente()->nombre_completo.' canceló su cita del '.$cita->fecha->format('d/m/Y'),
            'url' => route('citas.index'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Tu cita fue cancelada.');
    }

    private function ownCita(Cita $cita): void
    {
        abort_unless($cita->paciente_id === $this->paciente()->id && $cita->estado === 'pendiente', 403);
    }

    public function confirmar(Cita $cita)
    {
        $this->ownCita($cita);
        $cita->update(['estado' => 'confirmada']);

        Notificacion::crear($cita->empresa_id, 'Cita confirmada por el paciente', [
            'tipo' => 'cita', 'icono' => 'fa-circle-check',
            'mensaje' => $this->paciente()->nombre_completo.' confirmó su cita del '.$cita->fecha->format('d/m/Y'),
            'url' => route('citas.index'),
        ]);

        return back()->with('ok', 'Confirmaste tu asistencia. ¡Te esperamos!');
    }

    public function encuestar(Cita $cita)
    {
        abort_unless($cita->paciente_id === $this->paciente()->id && $cita->estado === 'atendida', 403);
        abort_if($cita->encuesta()->exists(), 403);

        return view('portal.encuesta', ['cita' => $cita]);
    }

    public function guardarEncuesta(Request $request, Cita $cita)
    {
        abort_unless($cita->paciente_id === $this->paciente()->id && $cita->estado === 'atendida', 403);
        abort_if($cita->encuesta()->exists(), 403);

        $data = $request->validate([
            'puntuacion' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        \App\Models\Encuesta::create([
            'empresa_id' => $cita->empresa_id,
            'paciente_id' => $cita->paciente_id,
            'cita_id' => $cita->id,
            'puntuacion' => $data['puntuacion'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        Notificacion::crear($cita->empresa_id, 'Nueva encuesta de satisfacción', [
            'tipo' => 'info', 'icono' => 'fa-star',
            'mensaje' => $this->paciente()->nombre_completo.' calificó su atención con '.$data['puntuacion'].'/5',
            'url' => route('reportes.clinico'),
        ]);

        return redirect()->route('portal.dashboard')->with('ok', 'Gracias por tu opinión.');
    }
}