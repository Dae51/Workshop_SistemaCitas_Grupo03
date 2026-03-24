<?php

namespace Database\Factories;

use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Si no le mandamos nada específico, por defecto tira al azar cualquier rol para rellenar
        $role = fake()->randomElement(['admin', 'medico', 'paciente']);
        
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => $role,
            // Magia acá: si es médico le asigna una especialidad al azar que ya esté creada, sino lo deja en null
            'especialidad_id' => $role === 'medico' 
                ? Especialidad::inRandomOrder()->first()->id ?? Especialidad::factory() 
                : null,
        ];
    }
    
    // Armé este "State" personalizado para no tenerle que pasar arrays a mano en el seeder. 
    // Solo lo llamo como User::factory()->medico()->create() y listo, lo fuerza a que sea médico. 
    public function medico(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'medico',
            'especialidad_id' => Especialidad::inRandomOrder()->first()?->id ?? Especialidad::factory(),
        ]);
    }

    // Este lo hice igual que el de arriba, estado específico para crear un paciente rápido
    public function paciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'paciente',
            'especialidad_id' => null,
        ]);
    }
    
    // Estado específico de admin por si necesitamos rellenar con ellos también
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'especialidad_id' => null,
        ]);
    }
}
