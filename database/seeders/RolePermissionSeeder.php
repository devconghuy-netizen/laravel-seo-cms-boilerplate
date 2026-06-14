<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $permissions = [
            'view-posts' => ['module' => 'content', 'resource' => 'posts', 'action' => 'view'],
            'create-post' => ['module' => 'content', 'resource' => 'posts', 'action' => 'create'],
            'edit-post' => ['module' => 'content', 'resource' => 'posts', 'action' => 'edit'],
            'delete-post' => ['module' => 'content', 'resource' => 'posts', 'action' => 'delete'],
            'publish-post' => ['module' => 'content', 'resource' => 'posts', 'action' => 'publish'],
            'view-categories' => ['module' => 'content', 'resource' => 'categories', 'action' => 'view'],
            'create-category' => ['module' => 'content', 'resource' => 'categories', 'action' => 'create'],
            'edit-category' => ['module' => 'content', 'resource' => 'categories', 'action' => 'edit'],
            'delete-category' => ['module' => 'content', 'resource' => 'categories', 'action' => 'delete'],
            'view-tags' => ['module' => 'content', 'resource' => 'tags', 'action' => 'view'],
            'create-tag' => ['module' => 'content', 'resource' => 'tags', 'action' => 'create'],
            'edit-tag' => ['module' => 'content', 'resource' => 'tags', 'action' => 'edit'],
            'delete-tag' => ['module' => 'content', 'resource' => 'tags', 'action' => 'delete'],
            'view-affiliate-links' => ['module' => 'monetization', 'resource' => 'affiliate_links', 'action' => 'view'],
            'create-affiliate-link' => ['module' => 'monetization', 'resource' => 'affiliate_links', 'action' => 'create'],
            'edit-affiliate-link' => ['module' => 'monetization', 'resource' => 'affiliate_links', 'action' => 'edit'],
            'delete-affiliate-link' => ['module' => 'monetization', 'resource' => 'affiliate_links', 'action' => 'delete'],
            'manage-users' => ['module' => 'admin', 'resource' => 'users', 'action' => 'manage'],
            'manage-roles' => ['module' => 'admin', 'resource' => 'roles', 'action' => 'manage'],
            'manage-permissions' => ['module' => 'admin', 'resource' => 'permissions', 'action' => 'manage'],
        ];

        foreach ($permissions as $name => $attributes) {
            Permission::updateOrCreate(['name' => $name], $attributes + ['is_system' => true]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $authorRole = Role::firstOrCreate(['name' => 'author']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminPermissions = Permission::all()->pluck('id')->toArray();
        $adminRole->permissions()->sync($adminPermissions);

        $editorPermissions = Permission::whereIn('name', [
            'view-posts',
            'create-post',
            'edit-post',
            'delete-post',
            'publish-post',
            'view-categories',
            'create-category',
            'edit-category',
            'view-tags',
            'create-tag',
            'edit-tag',
            'delete-tag',
            'view-affiliate-links',
            'create-affiliate-link',
            'edit-affiliate-link',
            'delete-affiliate-link',
        ])->pluck('id')->toArray();
        $editorRole->permissions()->sync($editorPermissions);

        $authorPermissions = Permission::whereIn('name', [
            'view-posts',
            'create-post',
            'edit-post',
            'view-categories',
        ])->pluck('id')->toArray();
        $authorRole->permissions()->sync($authorPermissions);

        $userPermissions = Permission::whereIn('name', [
            'view-posts',
            'view-categories',
        ])->pluck('id')->toArray();
        $userRole->permissions()->sync($userPermissions);
    }
}
