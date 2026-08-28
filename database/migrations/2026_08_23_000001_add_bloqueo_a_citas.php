<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->boolean('es_bloqueo')->default(false)->after('estado');
            $table->string('bloqueo_grupo')->nullable()->after('es_bloqueo');
        });

        // Permitir paciente_id nulo (solo se usará null cuando es_bloqueo = true)
        // Se usa SQL directo para no depender del paquete doctrine/dbal.
        DB::statement('ALTER TABLE citas MODIFY paciente_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['es_bloqueo', 'bloqueo_grupo']);
        });

        DB::statement('ALTER TABLE citas MODIFY paciente_id BIGINT UNSIGNED NOT NULL');
    }
};
