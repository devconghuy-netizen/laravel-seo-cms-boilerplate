<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function manageAll(User $user): bool
    {
        return $user->hasRole('admin', 'editor');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create-post');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->hasPermission('edit-post') && $this->canManagePost($user, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->hasPermission('delete-post') && $this->canManagePost($user, $post);
    }

    public function publish(User $user, Post|string|null $post = null): bool
    {
        if (! $user->hasPermission('publish-post')) {
            return false;
        }

        return ! $post instanceof Post || $this->canManagePost($user, $post);
    }

    private function canManagePost(User $user, Post $post): bool
    {
        return $post->author_id === $user->id || $user->hasRole('admin', 'editor');
    }
}
