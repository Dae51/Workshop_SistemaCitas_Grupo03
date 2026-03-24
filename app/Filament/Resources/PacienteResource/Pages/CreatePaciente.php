<?php

namespace App\Filament\Resources\PacienteResource\Pages;

use App\Filament\Resources\PacienteResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreatePaciente extends CreateRecord
{
    protected static string $resource = PacienteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = User::ROLE_PACIENTE;
        $data['activo'] = true;
        $data['password'] = Hash::make('password');

        return $data;
    }
}
