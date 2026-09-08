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

    private function turnoAbierto(): ?CierreCaja
    {
        return CierreCaja::where('empresa_id', $this->empresaId())->whereNull('cerrado_at')->latest()->first();
    }

    private function ultimoTurnoCerrado(): ?CierreCaja
    {
        return CierreCaja::where('empresa_id', $this->empresaId())->whereNotNull('cerrado_at')->orderByDesc('cerrado_at')->first();
    }

    private function resumenDelTurno(CierreCaja $cierre): array
    {
        $desde = $cierre->created_at;
        $hasta = $cierre->cerrado_at ?? now();

        $pagosTurno = Pago::where('empresa_id', $this->empresaId())
            ->where('estado', 'pagado')
            ->whereBetween('created_at', [$desde, $hasta])
            ->get();

        return [
            'porMetodo' => $pagosTurno->groupBy('metodo')->map(fn ($grupo) => $grupo->sum('monto')),
            'totalSistema' => $pagosTurno->sum('monto'),
            'efectivoSistema' => $pagosTurno->where('metodo', 'efectivo')->sum('monto'),
        ];
    }

    public function index()
    {
        $cierre = $this->turnoAbierto();
        $resumen = $cierre
            ? $this->resumenDelTurno($cierre)
            : ['porMetodo' => collect(), 'totalSistema' => 0, 'efectivoSistema' => 0];

        $ultimoCerrado = $this->ultimoTurnoCerrado();
        $fondoSugerido = $ultimoCerrado->efectivo_contado ?? 0;

        $historial = CierreCaja::where('empresa_id', $this->empresaId())
            ->whereNotNull('cerrado_at')
            ->with(['usuario', 'abiertoPor'])
            ->orderByDesc('cerrado_at')
            ->take(30)
            ->get();

        return view('cierres.index', array_merge(
            compact('cierre', 'historial', 'fondoSugerido'),
            $resumen
        ));
    }

    public function abrir(Request $request)
    {
        $data = $request->validate([
            'efectivo_inicial' => ['required', 'numeric', 'min:0'],
            'turno_nombre' => ['nullable', 'string', 'max:100'],
            'motivo_ajuste_fondo' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->turnoAbierto()) {
            return back()->withErrors(['efectivo_inicial' => 'Ya hay un turno abierto. Ciérralo antes de abrir uno nuevo.']);
        }

        $ultimoCerrado = $this->ultimoTurnoCerrado();
        $fondoEsperado = $ultimoCerrado->efectivo_contado ?? null;
        $fondoNuevo = round((float) $data['efectivo_inicial'], 2);

        if ($fondoEsperado !== null && $fondoNuevo !== round((float) $fondoEsperado, 2) && empty($data['motivo_ajuste_fondo'])) {
            return back()->withErrors([
                'motivo_ajuste_fondo' => 'El fondo inicial ('.number_format($fondoNuevo, 2).') es distinto al efectivo contado en el último turno ('.number_format($fondoEsperado, 2).'). Escribe el motivo del ajuste para continuar.',
            ])->withInput();
        }

        CierreCaja::create([
            'empresa_id' => $this->empresaId(),
            'user_id' => auth()->id(),
            'abierto_por_id' => auth()->id(),
            'fecha' => now()->toDateString(),
            'turno_nombre' => $data['turno_nombre'] ?? null,
            'efectivo_inicial' => $data['efectivo_inicial'],
            'motivo_ajuste_fondo' => $data['motivo_ajuste_fondo'] ?? null,
        ]);

        return redirect()->route('cierres.index')->with('ok', 'Turno abierto correctamente.');
    }

    public function cerrar(Request $request)
    {
        $data = $request->validate([
            'efectivo_contado' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $turno = $this->turnoAbierto();

        if (! $turno) {
            return back()->withErrors(['efectivo_contado' => 'No hay ningún turno abierto para cerrar.']);
        }

        $resumen = $this->resumenDelTurno($turno);

        $turno->update([
            'user_id' => auth()->id(),
            'efectivo_sistema' => $resumen['efectivoSistema'],
            'total_sistema' => $resumen['totalSistema'],
            'efectivo_contado' => $data['efectivo_contado'],
            'observaciones' => $data['observaciones'] ?? null,
            'cerrado_at' => now(),
        ]);

        return redirect()->route('cierres.index')->with('ok', 'Turno cerrado correctamente.');
    }

    public function reabrir(Request $request, CierreCaja $cierre)
    {
        abort_unless($cierre->empresa_id === $this->empresaId(), 403);

        if (! $cierre->estaCerrado()) {
            return back()->withErrors(['reabrir' => 'Este turno no está cerrado.']);
        }

        $abierto = $this->turnoAbierto();
        if ($abierto && $abierto->id !== $cierre->id) {
            return back()->withErrors(['reabrir' => 'Ya hay otro turno abierto ahora mismo. Ciérralo antes de reabrir este.']);
        }

        $cierre->update(['cerrado_at' => null]);

        return redirect()->route('cierres.index')->with('ok', 'Turno reabierto correctamente.');
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