<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpié la tabla por defecto de Laravel porque no íbamos a ocupar ni sesiones ni tokens raros.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            
            // Usé un enum para forzar que solo existan estos tres roles en la app
            $table->enum('role', ['admin', 'medico', 'paciente']);
            
            // Esta foránea es nullable porque los pacientes y admins no tienen especialidad.
            // Le metí nullOnDelete para que si hipotéticamente borramos una especialidad, no se borre el médico y solo quede en null
            $table->foreignId('especialidad_id')
                  ->nullable()
                  ->constrained('especialidades')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
