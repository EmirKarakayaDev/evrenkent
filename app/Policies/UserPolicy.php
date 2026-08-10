<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Kullanıcı/rol yönetimi sadece Süper Admin'e aittir.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('super_admin') && $user->isNot($model);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('super_admin') && $user->isNot($model);
    }
}
