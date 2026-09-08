<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primero se crea un índice normal para empresa_id, porque el índice único
        // que vamos a borrar es el que hoy sostiene la llave foránea de empresa_id.
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->index('empresa_id', 'cierres_caja_empresa_id_idx');
        });

        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropUnique(['empresa_id', 'fecha']);
            $table->string('turno_nombre', 100)->nullable()->after('fecha');
            $table->text('motivo_ajuste_fondo')->nullable()->after('efectivo_inicial');
        });
    }

    public function down(): void
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropColumn(['turno_nombre', 'motivo_ajuste_fondo']);
            $table->unique(['empresa_id', 'fecha']);
        });

        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropIndex('cierres_caja_empresa_id_idx');
        });
    }
};