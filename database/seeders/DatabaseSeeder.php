<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primero aseguré que se creen las especialidades para que no salte error cuando el doctor las busque por su foránea
        Especialidad::factory(20)->create();

        // Me hice estas cuentas quemadas acá para poder ingresar facilísimo al sistema y probar sin andarle buscando correos al azar
        User::factory()->admin()->create([
            'name' => 'Admin General',
            'email' => 'admin@test.com',
        ]);
        User::factory(4)->admin()->create(); // Relleno para que haya más admins y cumpla con los 20 q pide la entrega

        User::factory()->medico()->create([
            'name' => 'Médico Prueba',
            'email' => 'medico@test.com',
        ]);
        
        // Generé 20 médicos nomás para cumplir lo del PDF de 20 por tabla
        User::factory(20)->medico()->create();

        User::factory()->paciente()->create([
            'name' => 'Paciente Prueba',
            'email' => 'paciente@test.com',
        ]);
        
        // Igualmente hice 20 pacientes por cualquier cosa
        User::factory(20)->paciente()->create();

        // Al final tiro las 25 citas que internamente en el factory agarran pacientes y médicos aleatorios del pool
        Cita::factory(25)->create();
    }
}
