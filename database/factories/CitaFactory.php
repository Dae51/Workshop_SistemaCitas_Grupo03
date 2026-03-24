<?php

namespace Database\Factories;

use App\Models\Cita;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    public function definition(): array
    {
        // Busca cualquier médico y paciente en la DB para agarrarlos y simularles la cita a ellos
        $medico = User::where('role', 'medico')->inRandomOrder()->first() ?? User::factory()->medico()->create();
        $paciente = User::where('role', 'paciente')->inRandomOrder()->first() ?? User::factory()->paciente()->create();
        $horario = $medico->horarios()->inRandomOrder()->first();

        if ($horario) {
            $date = $this->nextDateForDay($horario->dia_semana);
            $time = Carbon::createFromFormat('H:i:s', $horario->hora_inicio)
                ->addHours(fake()->numberBetween(0, max(0, Carbon::createFromFormat('H:i:s', $horario->hora_fin)->diffInHours(Carbon::createFromFormat('H:i:s', $horario->hora_inicio)) - 1)))
                ->format('H:i:s');
        } else {
            $date = fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d');
            $time = fake()->randomElement(['08:00:00', '09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00', '16:00:00']);
        }

        while (Cita::query()
            ->where('medico_id', $medico->id)
            ->where('fecha', $date)
            ->where('hora', $time)
            ->exists()) {
            $time = Carbon::createFromFormat('H:i:s', $time)->addHour()->format('H:i:s');
        }

        return [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha' => $date,
            'hora' => $time,
            'estado' => fake()->randomElement(['pendiente', 'confirmada', 'cancelada']),
        ];
    }

    protected function nextDateForDay(int $dayOfWeek): string
    {
        $date = now();

        while ($date->dayOfWeek !== $dayOfWeek) {
            $date = $date->copy()->addDay();
        }

        return $date->format('Y-m-d');
    }
}
