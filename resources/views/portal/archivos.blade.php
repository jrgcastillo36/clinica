@extends('portal.layout')
@section('title','Mis archivos')
@section('content')
    <h1 style="margin:0 0 6px">Mis archivos</h1>
    <p class="muted" style="margin:0 0 20px">Documentos y tareas compartidos por tu Psicólogo(a).</p>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Archivo</th><th>Categoría</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
            @forelse($archivos as $a)
                <tr>
                    <td class="flex gap">
                        <i class="fa-solid {{ $a->es_imagen ? 'fa-image' : 'fa-file' }}" style="color:var(--violet-2)"></i>
                        <b>{{ $a->nombre }}</b>
                    </td>
                    <td><span class="pill violet">{{ ucfirst($a->categoria) }}</span></td>
                    <td>{{ $a->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('portal.archivos.download', $a) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-download"></i> Descargar</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="empty"><i class="fa-solid fa-folder-open"></i><p>Tu médico aún no ha compartido archivos contigo.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
