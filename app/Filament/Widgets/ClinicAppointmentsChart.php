<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use Filament\Widgets\ChartWidget;

class ClinicAppointmentsChart extends ChartWidget
{
    protected static ?string $heading = 'Citas por dia';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = auth()->user();
        $start = now()->startOfWeek();

        $appointments = Cita::query()
            ->when($user?->isMedico(), fn ($query) => $query->where('medico_id', $user->id))
            ->whereBetween('fecha', [$start->toDateString(), $start->copy()->addDays(6)->toDateString()])
            ->get()
            ->groupBy(fn ($cita) => $cita->fecha->format('Y-m-d'));

        $labels = [];
        $data = [];

        foreach (range(0, 6) as $day) {
            $date = $start->copy()->addDays($day);
            $labels[] = $date->format('D');
            $data[] = $appointments->get($date->format('Y-m-d'), collect())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Citas',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
