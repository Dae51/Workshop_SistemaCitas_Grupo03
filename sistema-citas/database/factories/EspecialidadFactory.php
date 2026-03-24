<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EspecialidadFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Le metí las especialidades hardcodeadas así realista para que no tire texto de prueba o Lorem ipsum random del faker
            'nombre' => fake()->unique()->randomElement([
                'Cardiología', 'Pediatría', 'Dermatología', 'Neurología', 
                'Odontología', 'Oftalmología', 'Ginecología', 'Traumatología',
                'Psiquiatría', 'Oncología', 'Urología', 'Otorrinolaringología',
                'Gastroenterología', 'Endocrinología', 'Neumología', 'Reumatología',
                'Nefrología', 'Hematología', 'Infectología', 'Cirugía General',
                'Medicina Interna', 'Medicina Familiar', 'Nutrición'
            ]),
        ];
    }
}
