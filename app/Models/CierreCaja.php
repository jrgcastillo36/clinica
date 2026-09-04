<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CierreCaja extends Model
{
    protected $table = 'cierres_caja';

    protected $fillable = [
        'empresa_id', 'user_id', 'fecha',
        'efectivo_inicial', 'abierto_por_id',
        'efectivo_sistema', 'efectivo_contado', 'total_sistema',
        'observaciones', 'cerrado_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'efectivo_inicial' => 'decimal:2',
        'efectivo_sistema' => 'decimal:2',
        'efectivo_contado' => 'decimal:2',
        'total_sistema' => 'decimal:2',
        'cerrado_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function abiertoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abierto_por_id');
    }

    public function estaCerrado(): bool
    {
        return $this->cerrado_at !== null;
    }

    public function getEfectivoEsperadoAttribute(): float
    {
        return round((float) $this->efectivo_inicial + (float) $this->efectivo_sistema, 2);
    }

    public function getDiferenciaAttribute(): float
    {
        return round((float) $this->efectivo_contado - $this->efectivo_esperado, 2);
    }
}
