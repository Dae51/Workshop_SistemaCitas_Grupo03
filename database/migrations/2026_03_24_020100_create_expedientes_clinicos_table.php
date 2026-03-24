<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes_clinicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('tipo_sangre', 5)->nullable();
            $table->text('alergias')->nullable();
            $table->text('antecedentes')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_clinicos');
    }
};
