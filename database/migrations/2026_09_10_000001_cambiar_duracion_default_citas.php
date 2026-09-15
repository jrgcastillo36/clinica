<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE citas MODIFY duracion INT NOT NULL DEFAULT 90');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE citas MODIFY duracion INT NOT NULL DEFAULT 30');
    }
};