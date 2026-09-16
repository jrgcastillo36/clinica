<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->unsignedSmallInteger('limite_admin')->nullable()->after('limite_usuarios');
            $table->unsignedSmallInteger('limite_recepcion')->nullable()->after('limite_admin');
            $table->unsignedSmallInteger('limite_medico')->nullable()->after('limite_recepcion');
        });
    }

    public function down(): void
    {
        Schema::table('planes', function (Blueprint $table) {
            $table->dropColumn(['limite_admin', 'limite_recepcion', 'limite_medico']);
        });
    }
};