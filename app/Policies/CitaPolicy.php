<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isMedico() || $user->isAsistente();
    }

    public function view(User $user, Cita $cita): bool
    {
        return $user->isAdmin()
            || $user->isAsistente()
            || ($user->isMedico() && $cita->medico_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }

    public function update(User $user, Cita $cita): bool
    {
        return $user->isAdmin()
            || $user->isAsistente()
            || ($user->isMedico() && $cita->medico_id === $user->id);
    }

    public function delete(User $user, Cita $cita): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
