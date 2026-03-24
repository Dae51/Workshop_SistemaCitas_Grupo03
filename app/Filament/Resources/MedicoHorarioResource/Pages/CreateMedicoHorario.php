<?php

namespace App\Filament\Resources\MedicoHorarioResource\Pages;

use App\Filament\Resources\MedicoHorarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicoHorario extends CreateRecord
{
    protected static string $resource = MedicoHorarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->isMedico()) {
            $data['medico_id'] = auth()->id();
        }

        return $data;
    }
}
