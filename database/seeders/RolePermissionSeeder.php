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
        // Create permissions
        $permissions = [
            'view-posts',
            'create-post',
            'edit-post',
            'delete-post',
            'publish-post',
            'view-categories',
            'create-category',
            'edit-category',
            'delete-category',
            'manage-users',
            'manage-roles',
            'manage-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
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
