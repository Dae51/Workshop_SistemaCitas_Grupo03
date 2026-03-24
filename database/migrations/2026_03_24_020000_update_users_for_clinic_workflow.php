<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('paciente')->change();
            $table->boolean('activo')->default(true)->after('role');
        });

        DB::table('users')
            ->whereNull('activo')
            ->update(['activo' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activo');
            $table->enum('role', ['admin', 'medico', 'paciente'])->default('paciente')->change();
        });
    }
};
