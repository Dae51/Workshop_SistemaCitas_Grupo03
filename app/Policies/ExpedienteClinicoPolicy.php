<?php

namespace App\Policies;

use App\Models\ExpedienteClinico;
use App\Models\User;

class ExpedienteClinicoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isMedico() || $user->isAsistente();
    }

    public function view(User $user, ExpedienteClinico $expedienteClinico): bool
    {
        return $user->isAdmin() || $user->isMedico() || $user->isAsistente();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMedico();
    }

    public function update(User $user, ExpedienteClinico $expedienteClinico): bool
    {
        return $user->isAdmin() || $user->isMedico();
    }

    public function delete(User $user, ExpedienteClinico $expedienteClinico): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
