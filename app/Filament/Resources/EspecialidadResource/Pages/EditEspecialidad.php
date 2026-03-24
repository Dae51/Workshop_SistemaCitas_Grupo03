<?php

namespace App\Filament\Resources\EspecialidadResource\Pages;

use App\Filament\Resources\EspecialidadResource;
use Filament\Resources\Pages\EditRecord;

class EditEspecialidad extends EditRecord
{
    protected static string $resource = EspecialidadResource::class;

    protected function getRedirectUrl(): string
    {
        return EspecialidadResource::getUrl('index');
    }
}
