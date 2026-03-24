<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\ExpedienteClinico;
use App\Models\Especialidad;
use App\Models\MedicoHorario;
use App\Models\User;
use Carbon\Carbon;
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

        $this->seedDemoUsers();

        User::factory(4)->admin()->create(); // Relleno para que haya más admins y cumpla con los 20 q pide la entrega
        
        // Generé 20 médicos nomás para cumplir lo del PDF de 20 por tabla
        User::factory(20)->medico()->create();

        User::factory(10)->asistente()->create();
        
        // Igualmente hice 20 pacientes por cualquier cosa
        User::factory(20)->paciente()->create();

        User::where('role', 'medico')->get()->each(function (User $medico): void {
            $slots = [
                [1, '08:00:00', '11:00:00'],
                [3, '08:00:00', '11:00:00'],
                [4, '08:00:00', '11:00:00'],
                [6, '15:00:00', '17:00:00'],
            ];

            foreach ($slots as [$day, $start, $end]) {
                MedicoHorario::firstOrCreate([
                    'medico_id' => $medico->id,
                    'dia_semana' => $day,
                    'hora_inicio' => $start,
                    'hora_fin' => $end,
                ]);
            }
        });

        User::where('role', 'paciente')->get()->each(function (User $paciente): void {
            ExpedienteClinico::factory()->create([
                'paciente_id' => $paciente->id,
            ]);
        });

        $patients = User::where('role', 'paciente')->pluck('id')->values();
        $slots = collect();

        User::where('role', 'medico')
            ->with('horarios')
            ->get()
            ->each(function (User $medico) use ($slots): void {
                foreach ($medico->horarios as $horario) {
                    $date = $this->nextDateForDay($horario->dia_semana);
                    $current = Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
                    $end = Carbon::createFromFormat('H:i:s', $horario->hora_fin);

                    while ($current < $end) {
                        $slots->push([
                            'medico_id' => $medico->id,
                            'fecha' => $date,
                            'hora' => $current->format('H:i:s'),
                        ]);

                        $current->addHour();
                    }
                }
            });

        $slots
            ->shuffle()
            ->take(25)
            ->values()
            ->each(function (array $slot, int $index) use ($patients): void {
                Cita::create([
                    'paciente_id' => $patients[$index % $patients->count()],
                    'medico_id' => $slot['medico_id'],
                    'fecha' => $slot['fecha'],
                    'hora' => $slot['hora'],
                    'estado' => fake()->randomElement(['pendiente', 'confirmada', 'cancelada']),
                ]);
            });
    }

    protected function nextDateForDay(int $dayOfWeek): string
    {
        $date = now();

        while ($date->dayOfWeek !== $dayOfWeek) {
            $date = $date->copy()->addDay();
        }

        return $date->format('Y-m-d');
    }

    protected function seedDemoUsers(): void
    {
        $especialidadId = Especialidad::query()->inRandomOrder()->value('id');

        $demoUsers = [
            [
                'email' => 'admin@test.com',
                'name' => 'Admin General',
                'role' => 'admin',
                'especialidad_id' => null,
            ],
            [
                'email' => 'medico@test.com',
                'name' => 'Medico Prueba',
                'role' => 'medico',
                'especialidad_id' => $especialidadId,
            ],
            [
                'email' => 'asistente@test.com',
                'name' => 'Asistente Prueba',
                'role' => 'asistente',
                'especialidad_id' => null,
            ],
            [
                'email' => 'paciente@test.com',
                'name' => 'Paciente Prueba',
                'role' => 'paciente',
                'especialidad_id' => null,
            ],
        ];

        foreach ($demoUsers as $user) {
            User::factory()
                ->state($user)
                ->create();
        }
    }
}
