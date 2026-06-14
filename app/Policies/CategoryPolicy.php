<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function manage(User $user): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('view-categories');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('create-category');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('edit-category');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('delete-category');
    }
}
