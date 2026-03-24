<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();

            $table->unique(['medico_id', 'dia_semana', 'hora_inicio', 'hora_fin'], 'medico_horarios_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_horarios');
    }
};
