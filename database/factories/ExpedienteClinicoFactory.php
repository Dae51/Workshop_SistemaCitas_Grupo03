<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpedienteClinicoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paciente_id' => User::where('role', 'paciente')->inRandomOrder()->value('id') ?? User::factory()->paciente(),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'tipo_sangre' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'alergias' => fake()->sentence(),
            'antecedentes' => fake()->paragraph(),
            'medicamentos_actuales' => fake()->sentence(),
            'notas' => fake()->paragraph(),
        ];
    }
}
