<?php

namespace App\Filament\Resources\ExpedienteClinicoResource\Pages;

use App\Filament\Resources\ExpedienteClinicoResource;
use Filament\Resources\Pages\EditRecord;

class EditExpedienteClinico extends EditRecord
{
    protected static string $resource = ExpedienteClinicoResource::class;

    protected function getRedirectUrl(): string
    {
        return ExpedienteClinicoResource::getUrl('view', ['record' => $this->record]);
    }
}
