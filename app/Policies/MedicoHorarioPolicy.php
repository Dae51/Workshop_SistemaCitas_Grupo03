<?php

namespace App\Policies;

use App\Models\MedicoHorario;
use App\Models\User;

class MedicoHorarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente() || $user->isMedico();
    }

    public function view(User $user, MedicoHorario $medicoHorario): bool
    {
        return $user->isAdmin()
            || $user->isAsistente()
            || ($user->isMedico() && $medicoHorario->medico_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }

    public function update(User $user, MedicoHorario $medicoHorario): bool
    {
        return $user->isAdmin()
            || $user->isAsistente()
            || ($user->isMedico() && $medicoHorario->medico_id === $user->id);
    }

    public function delete(User $user, MedicoHorario $medicoHorario): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }
}
