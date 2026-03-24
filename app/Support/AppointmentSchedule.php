<?php

namespace App\Support;

use App\Models\Cita;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSchedule
{
    public static function doctorWorksAt(User $doctor, string $date, string $time): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $normalizedTime = self::normalizeTime($time);

        return $doctor->horarios()
            ->where('dia_semana', $dayOfWeek)
            ->get()
            ->contains(function ($slot) use ($normalizedTime) {
                return $normalizedTime >= substr($slot->hora_inicio, 0, 5)
                    && $normalizedTime < substr($slot->hora_fin, 0, 5);
            });
    }

    public static function hasConflict(User $doctor, string $date, string $time, ?int $ignoreAppointmentId = null): bool
    {
        $query = Cita::query()
            ->where('medico_id', $doctor->id)
            ->whereDate('fecha', $date)
            ->whereTime('hora', self::normalizeTime($time));

        if ($ignoreAppointmentId !== null) {
            $query->whereKeyNot($ignoreAppointmentId);
        }

        return $query->exists();
    }

    public static function availableTimesForDoctor(?User $doctor): array
    {
        if (! $doctor) {
            return [];
        }

        $times = [];

        foreach ($doctor->horarios as $slot) {
            $current = Carbon::createFromFormat('H:i:s', $slot->hora_inicio);
            $end = Carbon::createFromFormat('H:i:s', $slot->hora_fin);

            while ($current < $end) {
                $time = $current->format('H:i');
                $times[$time] = self::dayLabel($slot->dia_semana) . ' - ' . $time;
                $current->addHour();
            }
        }

        ksort($times);

        return $times;
    }

    public static function normalizeTime(string $time): string
    {
        return Carbon::parse($time)->format('H:i:s');
    }

    public static function dayLabel(int $day): string
    {
        return \App\Models\MedicoHorario::DAYS[$day] ?? (string) $day;
    }
}
