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

        // Facturación electrónica: si está habilitada, genera el comprobante.
        $aviso = 'Pago registrado.';
        if ($pago->estado === 'pagado') {
            try {
                $comp = \App\Support\Facturacion::generarDesdePago($pago->load('paciente'));
                if ($comp) {
                    $aviso .= ' Comprobante '.$comp->numero.' ('.$comp->estado.').';
                }
            } catch (\Throwable $e) {
                // No interrumpe el registro del pago si la facturación falla.
            }
        }

        return redirect()->route('pagos.index')->with('ok', $aviso);
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
        $pago->update($this->validated($request));

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

    public function destroy(Pago $pago)
    {
        abort_unless($pago->empresa_id === $this->empresaId(), 403);
        $pago->delete();

        return redirect()->route('pagos.index')->with('ok', 'Pago eliminado.');
    }

    public function recibo(Pago $pago)
    {
        abort_unless($pago->empresa_id === $this->empresaId(), 403);
        $pago->load('paciente');
        $empresa = auth()->user()->empresa;
        $pdf = Pdf::loadView('pagos.recibo', compact('pago', 'empresa'))->setPaper('a6');

        return $pdf->stream('recibo-'.$pago->id.'.pdf');
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

        return $conServicioFijo->concat($conCategoria)->sortByDesc('fecha')->values();
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