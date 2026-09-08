<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'empresa_id', 'paciente_id', 'medico_id', 'especialidad_id',
        'fecha', 'hora', 'duracion', 'estado', 'motivo', 'notas',
        'es_teleconsulta', 'sala_video', 'estado_sala', 'hora_llegada', 'hora_atencion', 'consultorio_id',
        'es_bloqueo', 'bloqueo_grupo',
    ];

    protected $casts = ['fecha' => 'date', 'es_teleconsulta' => 'boolean', 'es_bloqueo' => 'boolean', 'hora_llegada' => 'datetime', 'hora_atencion' => 'datetime'];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function medico(): BelongsTo { return $this->belongsTo(User::class, 'medico_id'); }
    public function consultorio(): BelongsTo { return $this->belongsTo(\App\Models\Consultorio::class); }
    public function especialidad(): BelongsTo { return $this->belongsTo(Especialidad::class); }
    public function encuesta(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(Encuesta::class); }
    public function consulta(): \Illuminate\Database\Eloquent\Relations\HasOne { return $this->hasOne(\App\Models\Consulta::class); }
    public function pagos(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(\App\Models\Pago::class); }

    public function getSalaVideoUrlAttribute(): string
    {
        $sala = $this->sala_video ?: ('SuiteSalud-'.$this->empresa_id.'-'.$this->id.'-'.substr(md5('cita'.$this->id), 0, 8));
        return 'https://meet.jit.si/'.$sala;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $tel = preg_replace('/[^0-9]/', '', (string) optional($this->paciente)->telefono);
        if (! $tel) return null;

        $empresa = $this->empresa;
        $paciente = $this->paciente;
        $medico = $this->medico;

        // Emojis escritos como código Unicode (\u{...}) en vez de pegados directo,
        // para que nunca se dañen sin importar la codificación del editor/portapapeles.
        $eSol = "\u{2600}\u{FE0F}";
        $eCorazonAzul = "\u{1F499}";
        $eManosBlancas = "\u{1F90D}";
        $eFlor = "\u{1F338}";
        $ePersona = "\u{1F464}";
        $ePsicologo = "\u{1F9D1}\u{200D}\u{2695}\u{FE0F}";
        $eCalendario = "\u{1F4C5}";
        $eReloj1 = "\u{1F550}";
        $eReloj2 = "\u{1F55C}";
        $eNumeros = "\u{1F522}";
        $eCasa = "\u{1F3E0}";
        $ePin = "\u{1F4CC}";
        $eTarjeta = "\u{1F4B3}";
        $eSonrisa = "\u{1F601}";
        $eSonrisa2 = "\u{1F603}";
        $eBrillos = "\u{2728}";
        $eReloj3 = "\u{231B}";

        $fechaTexto = $this->fecha->locale('es')->isoFormat('dddd DD [de] MMMM');
        $fechaTexto = str_ireplace('septiembre', 'setiembre', $fechaTexto);
        $fechaTexto = ucfirst($fechaTexto);

        $inicio = \Carbon\Carbon::parse($this->fecha->format('Y-m-d').' '.$this->hora);
        $fin = $inicio->copy()->addMinutes((int) $this->duracion);

        $horaTexto = function (\Carbon\Carbon $t) {
            $formato = $t->format('h:i');
            $meridiano = $t->format('A') === 'AM' ? 'a. m.' : 'p. m.';
            return $formato.' '.$meridiano;
        };

        $numeroSesion = \App\Models\Cita::where('empresa_id', $this->empresa_id)
            ->where('paciente_id', $this->paciente_id)
            ->where('es_bloqueo', false)
            ->where('estado', 'atendida')
            ->where(function ($q) {
                $q->where('fecha', '<', $this->fecha)
                    ->orWhere(function ($q2) {
                        $q2->where('fecha', $this->fecha)->where('hora', '<', $this->hora);
                    });
            })
            ->count() + 1;

        $ordinal = $this->ordinalSesion($numeroSesion);

        $modalidad = $this->es_teleconsulta ? 'Virtual' : 'Presencial';
        $direccionTexto = $this->es_teleconsulta
            ? ''
            : "\n".$ePin." Dirección: ".($empresa->direccion ?? '—');

        $tieneSaldo = \App\Models\Consulta::where('empresa_id', $this->empresa_id)
            ->where('paciente_id', $this->paciente_id)
            ->whereNotNull('servicio_id')
            ->with(['servicio', 'pago' => fn ($q) => $q->where('estado', 'pagado')])
            ->get()
            ->contains(function ($c) {
                $precio = (float) ($c->servicio->precio ?? 0);
                $pagado = $c->pago->sum('monto');
                return ($precio - $pagado) > 0;
            });
        $estadoCuenta = $tieneSaldo ? 'Pago pendiente' : 'Al día';

        $tituloMedico = $medico->titulo_profesional ?: 'Ps.';

        $msg = $eSonrisa." ¡Hola! Buenos días ".$eSol." Esperando que se encuentre bien, le saludamos desde el ".($empresa->nombre ?? 'Centro Psicológico y Psicoterapéutico Libérate')." ".$eCorazonAzul.".\n"
            .$eBrillos." Queremos recordarle que tiene una sesión programada con nosotros. Estamos preparando todo para recibirle y acompañarle en este espacio. ".$eManosBlancas."\n\n"
            .$eFlor." DATOS DE SU SESIÓN\n"
            .$ePersona." Paciente: ".mb_strtoupper($paciente->nombre_completo ?? '', 'UTF-8')."\n"
            .$ePsicologo." Psicóloga(o): ".$tituloMedico.' '.mb_strtoupper($medico->name ?? '', 'UTF-8')."\n"
            .$eCalendario." Fecha: ".$fechaTexto."\n"
            .$eReloj1." Inicio: ".$horaTexto($inicio)."\n"
            .$eReloj2." Finalización: ".$horaTexto($fin)."\n"
            .$eNumeros." Sesión N.°: ".$ordinal."\n"
            .$eCasa." Modalidad: ".$modalidad
            .$direccionTexto."\n"
            .$eTarjeta." Estado de cuenta: ".$estadoCuenta."\n\n"
            .$eCorazonAzul." Por favor ¿Nos confirma su asistencia respondiendo \"Sí, asistiré\"?\n"
            ."Muchas gracias por confiar en Libérate. ".$eSonrisa2." Será un gusto recibirle y continuar acompañándole en su proceso. ".$eBrillos."\n\n"
            .$eReloj3." IMPORTANTE: Contamos con 5 minutos de tolerancia para el inicio de la sesión. Le recomendamos llegar unos minutos antes para ingresar con tranquilidad y comenzar a tiempo.";

        return 'https://wa.me/'.$tel.'?text='.rawurlencode($msg);
    }

    private function ordinalSesion(int $n): string
    {
        $especiales = [1 => '1era', 2 => '2da', 3 => '3era', 4 => '4ta', 5 => '5ta', 6 => '6ta', 7 => '7ma', 8 => '8va', 9 => '9na', 10 => '10ma'];
        return $especiales[$n] ?? $n.'va';
    }
}