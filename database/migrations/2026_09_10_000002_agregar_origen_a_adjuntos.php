<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adjuntos', function (Blueprint $table) {
            $table->string('origen')->default('staff')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('adjuntos', function (Blueprint $table) {
            $table->dropColumn('origen');
        });
    }
};