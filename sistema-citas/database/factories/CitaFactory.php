<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    public function definition(): array
    {
        // Busca cualquier médico y paciente en la DB para agarrarlos y simularles la cita a ellos
        $medico = User::where('role', 'medico')->inRandomOrder()->first() ?? User::factory()->medico()->create();
        $paciente = User::where('role', 'paciente')->inRandomOrder()->first() ?? User::factory()->paciente()->create();

        return [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            // Le quemé las horas exactas de una clínica para que no metiera minutos raros como '10:14:52'
            'hora' => fake()->randomElement(['08:00:00', '09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00', '16:00:00']),
            'estado' => fake()->randomElement(['pendiente', 'confirmada', 'cancelada']),
        ];
    }
}
