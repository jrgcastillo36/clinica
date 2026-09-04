<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('efectivo_sistema', 10, 2)->default(0);
            $table->decimal('efectivo_contado', 10, 2)->default(0);
            $table->decimal('total_sistema', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_caja');
    }
};
