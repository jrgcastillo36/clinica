<?php

namespace App\Http\Controllers;

use App\Models\Consulta;

class EstadoCuentaController extends Controller
{
    public function index()
    {
        $eid = (int) auth()->user()->empresa_id;

        $consultasConServicio = Consulta::where('empresa_id', $eid)
            ->whereNotNull('servicio_id')
            ->with(['paciente', 'servicio', 'pago' => fn ($q) => $q->where('estado', 'pagado')])
            ->get();

        $deudoresMap = [];

        foreach ($consultasConServicio as $c) {
            if (! $c->paciente) continue;

            $precio = (float) ($c->servicio->precio ?? 0);
            $pagado = $c->pago->sum('monto');
            $saldo = max($precio - $pagado, 0);

            if ($saldo <= 0) continue;

            $pid = $c->paciente_id;
            if (! isset($deudoresMap[$pid])) {
                $deudoresMap[$pid] = [
                    'paciente_id' => $pid,
                    'paciente' => $c->paciente,
                    'deuda' => 0,
                    'items' => 0,
                    'desde' => $c->fecha,
                ];
            }
            $deudoresMap[$pid]['deuda'] += $saldo;
            $deudoresMap[$pid]['items']++;
            if ($c->fecha->lt($deudoresMap[$pid]['desde'])) {
                $deudoresMap[$pid]['desde'] = $c->fecha;
            }
        }

        $deudores = collect($deudoresMap)->sortByDesc('deuda')->values();
        $deudores = $deudores->map(fn ($d) => (object) $d);

        $totalDeuda = $deudores->sum('deuda');

        return view('pagos.estados', compact('deudores', 'totalDeuda'));
    }
}