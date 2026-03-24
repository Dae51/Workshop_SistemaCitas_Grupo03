<?php

namespace App\Filament\Resources\MedicoHorarioResource\Pages;

use App\Filament\Resources\MedicoHorarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicoHorarios extends ListRecords
{
    protected static string $resource = MedicoHorarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
