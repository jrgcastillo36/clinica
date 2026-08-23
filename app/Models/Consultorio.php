<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultorio extends Model
{
    protected $fillable = ['empresa_id', 'nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
