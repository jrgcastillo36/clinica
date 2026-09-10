<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Pago;
use Illuminate\Http\Request;

class EstadoCuentaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $deudoresTodos = $this->construirDeudores();

        $totalDeuda = $deudoresTodos->sum('deuda');
        $totalVencido = $deudoresTodos->sum('dias_61_mas');

        $deudores = $q
            ? $deudoresTodos->filter(function ($d) use ($q) {
                return str_contains(mb_strtolower(optional($d->paciente)->nombre_completo ?? ''), mb_strtolower($q));
            })->values()
            : $deudoresTodos;

        return view('pagos.estados', compact('deudores', 'totalDeuda', 'totalVencido', 'q'));
    }

    /**
     * Totales resumidos de deuda, para reutilizar desde otras pantallas
     * (ej. el Resumen diario en Reportes) sin duplicar el cálculo.
     */
    public function resumenTotales(): array
    {
        $deudoresTodos = $this->construirDeudores();

        return [
            'totalDeuda' => $deudoresTodos->sum('deuda'),
            'totalVencido' => $deudoresTodos->sum('dias_61_mas'),
            'cantidadDeudores' => $deudoresTodos->count(),
        ];
    }

    /**
     * Arma la colección completa de deudores (consultas con servicio +
     * pagos directos sin consulta), con antigüedad calculada por cada uno.
     */
    protected function construirDeudores(): \Illuminate\Support\Collection
    {
        $eid = (int) auth()->user()->empresa_id;
        $hoy = now()->startOfDay();
        $deudoresMap = [];

        // 1. Consultas con servicio asignado (flujo normal)
        $consultasConServicio = Consulta::where('empresa_id', $eid)
            ->whereNotNull('servicio_id')
            ->with(['paciente', 'servicio', 'pago' => fn ($qr) => $qr->where('estado', 'pagado')])
            ->get();

        foreach ($consultasConServicio as $c) {
            if (! $c->paciente) continue;

            $precio = (float) ($c->servicio->precio ?? 0);
            $pagado = $c->pago->sum('monto');
            $saldo = max($precio - $pagado, 0);

            if ($saldo <= 0) continue;

            $this->acumularDeuda($deudoresMap, $c->paciente_id, $c->paciente, $saldo, $c->fecha, $hoy);
        }

        // 2. Pagos directos a una cita, sin consulta clínica de por medio
        $pagosDirectos = Pago::where('empresa_id', $eid)
            ->whereNull('consulta_id')
            ->whereNotNull('servicio_id')
            ->where('estado', 'pagado')
            ->with(['paciente', 'servicio'])
            ->get()
            ->groupBy(fn ($p) => $p->paciente_id.'-'.$p->cita_id.'-'.$p->servicio_id);

        foreach ($pagosDirectos as $grupo) {
            $primero = $grupo->first();
            if (! $primero->paciente) continue;

            $precio = (float) ($primero->servicio->precio ?? 0);
            $pagado = $grupo->sum('monto');
            $saldo = max($precio - $pagado, 0);

            if ($saldo <= 0) continue;

            $fechaMasAntigua = $grupo->min('fecha');
            $this->acumularDeuda($deudoresMap, $primero->paciente_id, $primero->paciente, $saldo, $fechaMasAntigua, $hoy);
        }

        return collect($deudoresMap)->sortByDesc('deuda')->values()->map(fn ($d) => (object) $d);
    }

    /**
     * Suma un monto de deuda al acumulado de un paciente, clasificándolo también
     * por antigüedad (0-30 / 31-60 / 61+ días desde la fecha del cargo).
     */
    private function acumularDeuda(array &$deudoresMap, int $pacienteId, $paciente, float $saldo, $fecha, $hoy): void
    {
        if (! isset($deudoresMap[$pacienteId])) {
            $deudoresMap[$pacienteId] = [
                'paciente_id' => $pacienteId,
                'paciente' => $paciente,
                'deuda' => 0,
                'items' => 0,
                'desde' => $fecha,
                'dias_0_30' => 0,
                'dias_31_60' => 0,
                'dias_61_mas' => 0,
            ];
        }

        $dias = (int) \Illuminate\Support\Carbon::parse($fecha)->diffInDays($hoy);

        if ($dias <= 30) {
            $deudoresMap[$pacienteId]['dias_0_30'] += $saldo;
        } elseif ($dias <= 60) {
            $deudoresMap[$pacienteId]['dias_31_60'] += $saldo;
        } else {
            $deudoresMap[$pacienteId]['dias_61_mas'] += $saldo;
        }

        $deudoresMap[$pacienteId]['deuda'] += $saldo;
        $deudoresMap[$pacienteId]['items']++;

        if (\Illuminate\Support\Carbon::parse($fecha)->lt(\Illuminate\Support\Carbon::parse($deudoresMap[$pacienteId]['desde']))) {
            $deudoresMap[$pacienteId]['desde'] = $fecha;
        }
    }
}