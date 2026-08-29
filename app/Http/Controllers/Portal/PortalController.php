<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    private function paciente()
    {
        return Auth::guard('paciente')->user();
    }

    public function dashboard()
    {
        $p = $this->paciente();
        $proximas = $p->citas()->with('especialidad', 'medico')
            ->whereDate('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')->orderBy('hora')->get();

        // Cita atendida pendiente de calificar
        $porCalificar = $p->citas()->with('especialidad', 'medico')
            ->where('estado', 'atendida')
            ->whereDoesntHave('encuesta')
            ->latest('fecha')->first();

        return view('portal.dashboard', [
            'paciente' => $p,
            'proximas' => $proximas,
            'porCalificar' => $porCalificar,
            'empresa' => $p->empresa,
        ]);
    }

    public function historia()
    {
        $p = $this->paciente();
        $consultas = $p->consultas()->with('medico', 'especialidad')->orderByDesc('fecha')->get();

        return view('portal.historia', ['paciente' => $p, 'consultas' => $consultas]);
    }
    public function descargarArchivo(\App\Models\Adjunto $adjunto)
    {
        $p = $this->paciente();
        abort_unless($adjunto->paciente_id === $p->id && $adjunto->visible_paciente, 403);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($adjunto->archivo, $adjunto->nombre);
    }



        public function archivos()
    {
        $p = $this->paciente();
        $archivos = \App\Models\Adjunto::where('paciente_id', $p->id)
            ->where('visible_paciente', true)
            ->orderByDesc('created_at')
            ->get();

        return view('portal.archivos', ['paciente' => $p, 'archivos' => $archivos]);
    }



        public function pagos()
    {
        $p = $this->paciente();
        $pagos = $p->pagos()->orderByDesc('fecha')->get();

        $totalPagado = $pagos->where('estado', 'pagado')->sum('monto');

        $totalPendiente = \App\Models\Consulta::where('paciente_id', $p->id)
            ->whereNotNull('servicio_id')
            ->with(['servicio', 'pago' => fn ($q) => $q->where('estado', 'pagado')])
            ->get()
            ->sum(function ($c) {
                $pagadoConsulta = $c->pago->sum('monto');
                $precio = $c->servicio->precio ?? 0;
                return max($precio - $pagadoConsulta, 0);
            });

        return view('portal.pagos', [
            'paciente' => $p,
            'pagos' => $pagos,
            'empresa' => $p->empresa,
            'totalPagado' => $totalPagado,
            'totalPendiente' => $totalPendiente,
        ]);
    }
}
