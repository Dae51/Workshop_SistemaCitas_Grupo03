<?php

namespace App\Filament\Resources\MedicoHorarioResource\Pages;

use App\Filament\Resources\MedicoHorarioResource;
use Filament\Resources\Pages\EditRecord;

class EditMedicoHorario extends EditRecord
{
    protected static string $resource = MedicoHorarioResource::class;

    protected function getRedirectUrl(): string
    {
        return MedicoHorarioResource::getUrl('index');
    }
}
