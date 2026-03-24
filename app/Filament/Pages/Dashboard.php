<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ClinicAppointmentsChart;
use App\Filament\Widgets\ClinicStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\MaxWidth;

class Dashboard extends BaseDashboard
{
    protected ?string $heading = 'Resumen de la clinica';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getHeaderWidgets(): array
    {
        return [
            ClinicStatsOverview::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            ClinicAppointmentsChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
}
