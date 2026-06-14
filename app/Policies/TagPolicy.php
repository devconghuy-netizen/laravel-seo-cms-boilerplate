<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function manage(User $user): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('view-tags');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('create-tag');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('edit-tag');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->hasRole('admin', 'editor') && $user->hasPermission('delete-tag');
    }
}
