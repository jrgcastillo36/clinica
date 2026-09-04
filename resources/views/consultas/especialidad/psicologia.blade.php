<div class="card pink mb">
    <h3 class="mb"><i class="fa-solid fa-brain" style="color:#8b5cf6"></i> Sesión de psicología</h3>
    <div class="form-grid">
        <div class="field"><label>N° de sesión</label><input type="number" name="datos[num_sesion]" value="{{ $d['num_sesion'] ?? '' }}"></div>
        <div class="field"><label>Enfoque / técnica</label>
            <select name="datos[tecnica]">
                @foreach(['Cognitivo-conductual','Humanista','Psicoanalítica','Sistémica','Mindfulness','Otro'] as $o)
                    <option @selected(($d['tecnica'] ?? '')==$o)>{{ $o }}</option>
                @endforeach
            </select></div>
               <div class="field">
            <label title="1 = muy bajo, 10 = muy bien">Estado de ánimo (1-10)</label>
            <input type="number" min="1" max="10" name="datos[animo]" value="{{ $d['animo'] ?? '' }}" placeholder="Ej. 7">
        </div>
        <div class="field">
            <label>Progreso terapéutico (%)</label>
            <input type="number" min="0" max="100" name="datos[progreso]" value="{{ $d['progreso'] ?? '' }}" placeholder="Ej. 60">
        </div>
        <div class="field"><label>Próxima sesión</label><input type="date" name="datos[siguiente_sesion]" value="{{ $d['siguiente_sesion'] ?? '' }}"></div>
    </div>
    <div class="field mt">
        <label>Notas de la sesión (qué se trabajó, observaciones clínicas)</label>
        <textarea name="datos[desarrollo]" rows="4">{{ $d['desarrollo'] ?? '' }}</textarea>
    </div>
    <div class="field mt"><label>Tareas / objetivos</label><textarea name="datos[tareas]">{{ $d['tareas'] ?? '' }}</textarea></div>
</div>