<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            
            // Amarro el paciente a la tabla de usuarios. Si alguien borra un paciente, 
            // le metí cascadeOnDelete para que se vuelen sus citas también y no queden huérfanos.
            $table->foreignId('paciente_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
                  
            // Lo mismo acá, amarramos el médico a la tabla de usuarios
            $table->foreignId('medico_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
                  
            $table->date('fecha');
            $table->time('hora');
            
            // El estado por defecto es pendiente para no tener que mandarlo siempre que metemos una cita en código
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
