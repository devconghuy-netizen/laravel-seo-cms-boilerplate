<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHealthStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_health_status_page(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);

        $this->actingAs($admin)
            ->get(route('admin.health.index'))
            ->assertOk()
            ->assertSee('System health')
            ->assertSee('Application')
            ->assertSee('Database')
            ->assertSee('Storage')
            ->assertSee('Cache')
            ->assertSee('Queue')
            ->assertSee('Session')
            ->assertSee('Logs');
    }

    public function test_user_without_manage_users_cannot_view_health_status_page(): void
    {
        $editor = $this->userWithRole('editor', ['view-posts']);

        $this->actingAs($editor)
            ->get(route('admin.health.index'))
            ->assertForbidden();
    }

    private function userWithRole(string $roleName, array $permissionNames): User
    {
        $role = Role::create(['name' => $roleName]);

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->permissions()->syncWithoutDetaching($permission);
        }

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
