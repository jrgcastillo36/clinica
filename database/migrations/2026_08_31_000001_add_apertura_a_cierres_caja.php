<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->decimal('efectivo_inicial', 10, 2)->default(0)->after('fecha');
            $table->foreignId('abierto_por_id')->nullable()->after('efectivo_inicial')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('cerrado_at')->nullable()->after('observaciones');
        });

        // efectivo_contado y total_sistema deben poder quedar en NULL mientras la caja está
        // abierta pero aún no cerrada. Se usa SQL directo para no depender de doctrine/dbal.
        DB::statement('ALTER TABLE cierres_caja MODIFY efectivo_contado DECIMAL(10,2) NULL');
        DB::statement('ALTER TABLE cierres_caja MODIFY total_sistema DECIMAL(10,2) NULL');
    }

    public function down(): void
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropConstrainedForeignId('abierto_por_id');
            $table->dropColumn(['efectivo_inicial', 'cerrado_at']);
        });
    }
};
