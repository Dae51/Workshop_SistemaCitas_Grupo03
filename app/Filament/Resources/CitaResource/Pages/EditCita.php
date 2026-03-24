<?php

namespace App\Filament\Resources\CitaResource\Pages;

use App\Filament\Resources\CitaResource;
use Filament\Resources\Pages\EditRecord;

class EditCita extends EditRecord
{
    protected static string $resource = CitaResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CitaResource::validateAppointment($data, $this->record);
    }

    protected function getRedirectUrl(): string
    {
        return CitaResource::getUrl('index');
    }
}
