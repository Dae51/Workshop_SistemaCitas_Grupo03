<?php

namespace App\Policies;

use App\Models\Especialidad;
use App\Models\User;

class EspecialidadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }

    public function view(User $user, Especialidad $especialidad): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Especialidad $especialidad): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Especialidad $especialidad): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
