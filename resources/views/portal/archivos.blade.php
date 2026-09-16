@extends('portal.layout')
@section('title','Mis archivos')
@section('content')
    <h1 style="margin:0 0 6px">Mis archivos</h1>
    <p class="muted" style="margin:0 0 20px">Documentos y tareas compartidos por tu psicólogo(a), y lo que tú mismo subas.</p>

    @if(session('ok'))<div class="alert ok"><i class="fa-solid fa-circle-check"></i> {{ session('ok') }}</div>@endif
    @if($errors->any())<div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('portal.archivos.subir') }}" enctype="multipart/form-data" class="card mb">
        @csrf
        <label style="display:block;margin-bottom:8px;font-weight:600;font-size:13.5px">Subir un archivo (ej. una tarea resuelta)</label>
        <div class="flex gap" style="flex-wrap:wrap;align-items:center">
            <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required style="flex:1;min-width:200px">
            <button class="btn btn-primary btn-sm"><i class="fa-solid fa-upload"></i> Subir</button>
        </div>
        <p class="muted mt" style="font-size:11.5px">Solo PDF o imágenes (JPG, PNG), hasta 10 MB.</p>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Archivo</th><th>Categoría</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
            @forelse($archivos as $a)
                <tr>
                    <td class="flex gap">
                        <i class="fa-solid {{ $a->es_imagen ? 'fa-image' : 'fa-file' }}" style="color:var(--violet-2)"></i>
                        <b>{{ $a->nombre }}</b>
                        @if($a->origen === 'paciente')<span class="pill" style="font-size:10px">Subido por ti</span>@endif
                    </td>
                    <td><span class="pill violet">{{ ucfirst($a->categoria) }}</span></td>
                    <td>{{ $a->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('portal.archivos.download', $a) }}" class="btn btn-light btn-sm"><i class="fa-solid fa-download"></i> Descargar</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="empty"><i class="fa-solid fa-folder-open"></i><p>Aún no hay archivos aquí. Tu psicólogo(a) puede compartir algo, o tú puedes subir uno arriba.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection