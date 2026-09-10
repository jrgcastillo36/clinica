<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'empresa_id', 'paciente_id', 'cita_id', 'consulta_id', 'servicio_id',
        'concepto', 'monto', 'metodo', 'estado', 'fecha', 'comprobante', 'notas',
        'cuota_numero', 'cuota_total',
        'motivo_anulacion', 'anulado_por_id', 'anulado_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'anulado_at' => 'datetime',
    ];

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function consulta(): BelongsTo { return $this->belongsTo(\App\Models\Consulta::class); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function cita(): BelongsTo { return $this->belongsTo(Cita::class); }
    public function servicio(): BelongsTo { return $this->belongsTo(Servicio::class); }
    public function anuladoPor(): BelongsTo { return $this->belongsTo(User::class, 'anulado_por_id'); }

    public function getMetodoLabelAttribute(): string
    {
        return [
            'efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia', 'yape_plin' => 'Yape / Plin', 'otro' => 'Otro',
        ][$this->metodo] ?? $this->metodo;
    }
}