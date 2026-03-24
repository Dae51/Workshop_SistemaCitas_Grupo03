<?php

namespace App\Filament\Resources\ExpedienteClinicoResource\Pages;

use App\Filament\Resources\ExpedienteClinicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpedientesClinicos extends ListRecords
{
    protected static string $resource = ExpedienteClinicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
