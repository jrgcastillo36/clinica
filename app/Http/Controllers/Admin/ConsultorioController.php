<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultorio;
use Illuminate\Http\Request;

class ConsultorioController extends Controller
{
    private function empresaId(): int
    {
        return (int) auth()->user()->empresa_id;
    }

    public function index()
    {
        $consultorios = Consultorio::where('empresa_id', $this->empresaId())->orderBy('nombre')->get();

        return view('admin.consultorios.index', compact('consultorios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
        ]);

        Consultorio::create([
            'empresa_id' => $this->empresaId(),
            'nombre' => $data['nombre'],
        ]);

        return back()->with('ok', 'Consultorio agregado.');
    }

    public function update(Request $request, Consultorio $consultorio)
    {
        abort_unless($consultorio->empresa_id === $this->empresaId(), 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
        ]);

        $consultorio->update([
            'nombre' => $data['nombre'],
            'activo' => $request->boolean('activo'),
        ]);

        return back()->with('ok', 'Consultorio actualizado.');
    }

    public function destroy(Consultorio $consultorio)
    {
        abort_unless($consultorio->empresa_id === $this->empresaId(), 403);
        $consultorio->delete();

        return back()->with('ok', 'Consultorio eliminado.');
    }
}
