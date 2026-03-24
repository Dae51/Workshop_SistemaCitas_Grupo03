<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente() || $user->isMedico();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($model->isPaciente()) {
            return $user->isAsistente() || $user->isMedico();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isAsistente();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isAsistente() && $model->isPaciente();
    }

    public function delete(User $user, User $model): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
