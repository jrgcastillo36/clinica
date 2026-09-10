<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Pago;
use App\Models\Paciente;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->endOfMonth()->toDateString());

        $pagos = Pago::with('paciente')
            ->where('empresa_id', $this->empresaId())
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderByDesc('fecha')->paginate(15)->withQueryString();

        $total = Pago::where('empresa_id', $this->empresaId())
            ->where('estado', 'pagado')
            ->whereBetween('fecha', [$desde, $hasta])->sum('monto');

        $pendiente = Pago::where('empresa_id', $this->empresaId())
            ->where('estado', 'pendiente')
            ->whereBetween('fecha', [$desde, $hasta])->sum('monto');

        return view('pagos.index', compact('pagos', 'total', 'pendiente', 'desde', 'hasta'));
    }

        public function create(Request $request)
    {
        return view('pagos.form', [
            'pago' => new Pago(['fecha' => now()->toDateString(), 'estado' => 'pagado']),
            'pacientes' => $this->pacientes(),
            'pacienteSel' => $request->get('paciente_id'),
            'citaSel' => $request->get('cita_id'),
            'servicios' => $this->servicios(),
            'consultasPendientes' => $this->consultasPendientesCobro(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['empresa_id'] = $this->empresaId();

        // Si no hay consulta clínica vinculada pero sí se eligió un servicio del
        // catálogo, se guarda ese servicio directo en el pago para poder calcular
        // su saldo pendiente igual que se hace con las consultas.
        if (! $request->filled('consulta_id') && $request->filled('servicio_elegido_id')) {
            $data['servicio_id'] = $request->servicio_elegido_id;
        }

        $pago = Pago::create($data);

        // Si esta consulta era tipo "categoría" (sin precio fijo) y recepción eligió
        // un código específico al cobrar, se "fija" ese servicio/precio en la consulta
        // para que los próximos cobros calculen el saldo restante correctamente.
        if ($request->filled('consulta_id') && $request->filled('servicio_elegido_id')) {
            \App\Models\Consulta::where('id', $request->consulta_id)
                ->where('empresa_id', $this->empresaId())
                ->whereNull('servicio_id')
                ->update(['servicio_id' => $request->servicio_elegido_id]);
        }

        Notificacion::crear($pago->empresa_id, 'Pago registrado', [
            'tipo' => 'pago', 'icono' => 'fa-money-bill-wave',
            'mensaje' => $pago->concepto.' · '.number_format((float) $pago->monto, 2),
            'url' => route('pagos.index'),
        ]);

        return redirect()->route('pagos.index')->with('ok', 'Pago registrado.');
    }

      public function edit(Pago $pago)
    {
        abort_unless($pago->empresa_id === $this->empresaId(), 403);
        return view('pagos.form', [
            'pago' => $pago,
            'pacientes' => $this->pacientes(),
            'pacienteSel' => $pago->paciente_id,
            'citaSel' => $pago->cita_id,
            'servicios' => $this->servicios(),
            'consultasPendientes' => $this->consultasPendientesCobro(),
        ]);
    }

    public function update(Request $request, Pago $pago)
    {
        abort_unless($pago->empresa_id === $this->empresaId(), 403);
        $data = $this->validated($request);

        if (! $request->filled('consulta_id') && $request->filled('servicio_elegido_id')) {
            $data['servicio_id'] = $request->servicio_elegido_id;
        }

        $pago->update($data);

        // Mismo ajuste que en store(): fija el servicio/precio elegido en la
        // consulta la primera vez que se cobra un pendiente tipo "categoría".
        if ($request->filled('consulta_id') && $request->filled('servicio_elegido_id')) {
            \App\Models\Consulta::where('id', $request->consulta_id)
                ->where('empresa_id', $this->empresaId())
                ->whereNull('servicio_id')
                ->update(['servicio_id' => $request->servicio_elegido_id]);
        }

        return redirect()->route('pagos.index')->with('ok', 'Pago actualizado.');
    }

    public function anular(Request $request, Pago $pago)
    {
        abort_unless($pago->empresa_id === $this->empresaId(), 403);
        abort_unless(auth()->user()->role === 'admin', 403, 'Solo un administrador puede anular pagos.');

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $pago->update([
            'estado' => 'anulado',
            'motivo_anulacion' => $data['motivo'],
            'anulado_por_id' => auth()->id(),
            'anulado_at' => now(),
        ]);

        return redirect()->route('pagos.index')->with('ok', 'Pago anulado. Queda registrado en el historial con el motivo indicado.');
    }

    public function recibo(Pago $pago)
    {
        abort_unless($pago->empresa_id === $this->empresaId(), 403);
        $pago->load('paciente');
        $empresa = auth()->user()->empresa;
        $pdf = Pdf::loadView('pagos.recibo', compact('pago', 'empresa'))->setPaper('a6');

        return $pdf->stream('recibo-'.$pago->id.'.pdf');
    }

    public function estadoCuentaPdf(Paciente $paciente)
    {
        abort_unless($paciente->empresa_id === $this->empresaId(), 403);

        $movimientos = collect();

        // 1. Cargos y pagos de consultas con servicio asignado (flujo normal)
        $consultas = \App\Models\Consulta::where('paciente_id', $paciente->id)
            ->where('empresa_id', $this->empresaId())
            ->whereNotNull('servicio_id')
            ->with(['servicio', 'pago' => fn ($q) => $q->where('estado', 'pagado')->orderBy('fecha')])
            ->orderBy('fecha')
            ->get();

        foreach ($consultas as $c) {
            $precio = (float) ($c->servicio->precio ?? 0);
            $movimientos->push([
                'fecha' => $c->fecha,
                'tipo' => 'cargo',
                'descripcion' => $c->servicio->nombre ?? 'Servicio',
                'monto' => $precio,
            ]);

            foreach ($c->pago as $p) {
                $movimientos->push([
                    'fecha' => $p->fecha,
                    'tipo' => 'pago',
                    'descripcion' => 'Pago — '.$p->metodo_label,
                    'monto' => -1 * (float) $p->monto,
                ]);
            }
        }

        // 2. Cargos y pagos directos a una cita, sin consulta clínica de por medio
        $pagosDirectos = Pago::where('paciente_id', $paciente->id)
            ->where('empresa_id', $this->empresaId())
            ->whereNull('consulta_id')
            ->whereNotNull('servicio_id')
            ->where('estado', 'pagado')
            ->with('servicio')
            ->orderBy('fecha')
            ->get()

->groupBy(fn ($p) => $p->paciente_id.'-'.$p->cita_id.'-'.$p->servicio_id);

        foreach ($pagosDirectos as $grupo) {

            $primero = $grupo->first();
            $precio = (float) ($primero->servicio->precio ?? 0);
            $movimientos->push([
                'fecha' => $primero->fecha,
                'tipo' => 'cargo',
                'descripcion' => $primero->servicio->nombre ?? 'Servicio',
                'monto' => $precio,
            ]);

            foreach ($grupo as $p) {
                $movimientos->push([
                    'fecha' => $p->fecha,
                    'tipo' => 'pago',
                    'descripcion' => 'Pago — '.$p->metodo_label,
                    'monto' => -1 * (float) $p->monto,
                ]);
            }
        }

        $movimientos = $movimientos->sortBy('fecha')->values();

        $saldoCorrido = 0;
        $movimientos = $movimientos->map(function ($m) use (&$saldoCorrido) {
            $saldoCorrido += $m['monto'];
            $m['saldo'] = $saldoCorrido;
            return $m;
        });

        $totalCargos = $movimientos->where('tipo', 'cargo')->sum('monto');
        $totalPagos = abs($movimientos->where('tipo', 'pago')->sum('monto'));
        $saldoFinal = $saldoCorrido;

        $pdf = Pdf::loadView('pagos.estado-cuenta-paciente', [
            'paciente' => $paciente,
            'empresa' => auth()->user()->empresa,
            'movimientos' => $movimientos,
            'totalCargos' => $totalCargos,
            'totalPagos' => $totalPagos,
            'saldoFinal' => $saldoFinal,
            'generadoEl' => now(),
        ])->setPaper('a4');

        return $pdf->stream('estado-cuenta-'.$paciente->id.'.pdf');
    }

       private function servicios()
    {
        return Servicio::where('empresa_id', $this->empresaId())->where('activo', true)->orderBy('nombre')->get();
    }

   private function consultasPendientesCobro()
{
    // Sistema viejo: consulta con un servicio de precio fijo (calcula saldo real)
    $conServicioFijo = \App\Models\Consulta::where('empresa_id', $this->empresaId())
        ->whereNotNull('servicio_id')
        ->with(['paciente', 'servicio', 'pago' => fn ($q) => $q->where('estado', 'pagado')])
        ->get()
        ->filter(function ($c) {
            $pagado = $c->pago->sum('monto');
            $precio = $c->servicio->precio ?? 0;
            return $pagado < $precio;
        })
        ->map(function ($c) {
            $c->tipoPendiente = 'servicio_fijo';
            return $c;
        });

    // Sistema nuevo: médico solo asignó una categoría, recepción define el código exacto
    $conCategoria = \App\Models\Consulta::where('empresa_id', $this->empresaId())
        ->whereNotNull('categoria_servicio')
        ->whereDoesntHave('pago')
        ->with(['paciente'])
        ->get()
        ->map(function ($c) {
            $c->tipoPendiente = 'categoria';
            return $c;
        });

    // Pagos directos a una cita, sin consulta clínica de por medio, con saldo pendiente
    $conPagoDirecto = \App\Models\Pago::where('empresa_id', $this->empresaId())
        ->whereNull('consulta_id')
        ->whereNotNull('servicio_id')
        ->where('estado', 'pagado')
        ->with(['paciente', 'servicio'])
        ->get()
->groupBy(fn ($p) => $p->paciente_id.'-'.$p->cita_id.'-'.$p->servicio_id)
        ->map(function ($grupo) {
            $primero = $grupo->first();
            $precio = (float) ($primero->servicio->precio ?? 0);
            $pagado = $grupo->sum('monto');
            $saldo = max($precio - $pagado, 0);

            if ($saldo <= 0 || ! $primero->paciente) {
                return null;
            }

            return (object) [
'id' => 'pd-'.$primero->paciente_id.'-'.$primero->cita_id.'-'.$primero->servicio_id,
            'paciente_id' => $primero->paciente_id,
                'paciente' => $primero->paciente,
                'tipoPendiente' => 'pago_directo',
                'servicio' => $primero->servicio,
                'servicio_id' => $primero->servicio_id,
                'saldo' => $saldo,
                'pagado' => $pagado,
                'total' => $precio,
                'fecha' => $grupo->min('fecha'),
            ];
        })
        ->filter()
        ->values();

    return $conServicioFijo->concat($conCategoria)->concat($conPagoDirecto)->sortByDesc('fecha')->values();
}

    private function pacientes()
    {
        return Paciente::where('empresa_id', $this->empresaId())->orderBy('apellidos')->get();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'concepto' => ['required', 'string', 'max:150'],
            'monto' => ['required', 'numeric', 'min:0'],
            'metodo' => ['required', 'in:efectivo,tarjeta,transferencia,yape_plin,otro'],
            'estado' => ['required', 'in:pendiente,pagado,anulado'],
            'fecha' => ['required', 'date'],
            'notas' => ['nullable', 'string'],
                        'cuota_numero' => ['nullable', 'integer', 'min:1'],
            'cuota_total' => ['nullable', 'integer', 'min:1'],
            'consulta_id' => ['nullable', 'exists:consultas,id'],
            'cita_id' => ['nullable', 'exists:citas,id'],
        ]);
            
    }
}