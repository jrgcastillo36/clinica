<?php

namespace App\Http\Controllers;

use App\Models\CierreCaja;
use App\Models\Pago;
use Illuminate\Http\Request;

class CierreCajaController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    private function resumenDelDia(string $fecha): array
    {
        $pagosHoy = Pago::where('empresa_id', $this->empresaId())
            ->where('estado', 'pagado')
            ->whereDate('fecha', $fecha)
            ->get();

        return [
            'porMetodo' => $pagosHoy->groupBy('metodo')->map(fn ($grupo) => $grupo->sum('monto')),
            'totalSistema' => $pagosHoy->sum('monto'),
            'efectivoSistema' => $pagosHoy->where('metodo', 'efectivo')->sum('monto'),
        ];
    }

    public function index()
    {
        $fecha = now()->toDateString();

        $cierre = CierreCaja::where('empresa_id', $this->empresaId())->where('fecha', $fecha)->first();
        $resumen = $this->resumenDelDia($fecha);

        $historial = CierreCaja::where('empresa_id', $this->empresaId())
            ->whereNotNull('cerrado_at')
            ->with(['usuario', 'abiertoPor'])
            ->orderByDesc('fecha')
            ->take(30)
            ->get();

        return view('cierres.index', array_merge(compact('fecha', 'cierre', 'historial'), $resumen));
    }

    public function abrir(Request $request)
    {
        $data = $request->validate([
            'efectivo_inicial' => ['required', 'numeric', 'min:0'],
        ]);

        $fecha = now()->toDateString();

        if (CierreCaja::where('empresa_id', $this->empresaId())->where('fecha', $fecha)->exists()) {
            return back()->withErrors(['efectivo_inicial' => 'La caja de hoy ya fue abierta.']);
        }

        CierreCaja::create([
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
            'abierto_por_id' => auth()->id(),
            'fecha' => $fecha,
            'efectivo_inicial' => $data['efectivo_inicial'],
        ]);

        return redirect()->route('cierres.index')->with('ok', 'Caja abierta correctamente.');
    }

    public function cerrar(Request $request)
    {
        $data = $request->validate([
            'efectivo_contado' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $fecha = now()->toDateString();
        $cierre = CierreCaja::where('empresa_id', $this->empresaId())->where('fecha', $fecha)->first();

        if (! $cierre) {
            return back()->withErrors(['efectivo_contado' => 'Primero debes abrir la caja de hoy.']);
        }
        if ($cierre->estaCerrado()) {
            return back()->withErrors(['efectivo_contado' => 'El día de hoy ya fue cerrado.']);
        }

        $resumen = $this->resumenDelDia($fecha);

        $cierre->update([
            'user_id' => auth()->id(),
            'efectivo_sistema' => $resumen['efectivoSistema'],
            'total_sistema' => $resumen['totalSistema'],
            'efectivo_contado' => $data['efectivo_contado'],
            'observaciones' => $data['observaciones'] ?? null,
            'cerrado_at' => now(),
        ]);

        return redirect()->route('cierres.index')->with('ok', 'Día cerrado correctamente.');
    }

    public function pdf(CierreCaja $cierre)
    {
        abort_unless($cierre->empresa_id === $this->empresaId(), 403);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cierres.pdf', [
            'cierre' => $cierre,
            'empresa' => auth()->user()->empresa,
        ])->setPaper('a5');

        return $pdf->stream('cierre-caja-'.$cierre->fecha->format('Y-m-d').'.pdf');
    }
}
