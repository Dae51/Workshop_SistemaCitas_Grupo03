<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicoHorarioFactory extends Factory
{
    public function definition(): array
    {
        $blocks = [
            ['08:00:00', '11:00:00'],
            ['09:00:00', '12:00:00'],
            ['14:00:00', '17:00:00'],
            ['15:00:00', '17:00:00'],
        ];

        [$start, $end] = fake()->randomElement($blocks);

        return [
            'medico_id' => User::where('role', 'medico')->inRandomOrder()->value('id') ?? User::factory()->medico(),
            'dia_semana' => fake()->numberBetween(1, 6),
            'hora_inicio' => $start,
            'hora_fin' => $end,
        ];
    }
}
