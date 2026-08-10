<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Kategori yönetimi editoryal bir karar — sadece Süper Admin'e ait,
     * Dergi Editörü/Yazar'a açılmıyor.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->hasRole('super_admin');
    }
}
