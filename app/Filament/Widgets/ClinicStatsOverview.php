<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClinicStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        $patients = User::query()->where('role', User::ROLE_PACIENTE)->count();

        $todayAppointments = Cita::query()
            ->when($user?->isMedico(), fn ($query) => $query->where('medico_id', $user->id))
            ->whereDate('fecha', today())
            ->count();

        $pendingAppointments = Cita::query()
            ->when($user?->isMedico(), fn ($query) => $query->where('medico_id', $user->id))
            ->where('estado', 'pendiente')
            ->count();

        return [
            Stat::make('Pacientes', (string) $patients),
            Stat::make('Citas de hoy', (string) $todayAppointments),
            Stat::make('Citas pendientes', (string) $pendingAppointments),
        ];
    }
}
