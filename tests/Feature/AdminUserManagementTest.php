<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_users_index_and_filter_by_role(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);
        $authorRole = Role::create(['name' => 'author']);
        $editorRole = Role::create(['name' => 'editor']);
        $author = User::factory()->create(['name' => 'Author Person', 'email' => 'author@example.com']);
        $editor = User::factory()->create(['name' => 'Editor Person', 'email' => 'editor@example.com']);
        $author->assignRole($authorRole);
        $editor->assignRole($editorRole);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => 'author']))
            ->assertOk()
            ->assertSee('Quản lý user')
            ->assertSee('Author Person')
            ->assertDontSee('Editor Person');
    }

    public function test_admin_can_update_user_roles_and_status(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);
        $authorRole = Role::create(['name' => 'author']);
        $editorRole = Role::create(['name' => 'editor']);
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($authorRole);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'role_ids' => [$editorRole->id],
            ])
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();

        $this->assertFalse($user->is_active);
        $this->assertTrue($user->hasRole('editor'));
        $this->assertFalse($user->hasRole('author'));
    }

    public function test_editor_without_manage_users_permission_cannot_manage_users(): void
    {
        $editor = $this->userWithRole('editor', ['view-posts']);
        $user = User::factory()->create();

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->put(route('admin.users.update', $user), ['is_active' => '1'])
            ->assertForbidden();
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'role_ids' => $admin->roles()->pluck('roles.id')->all(),
            ])
            ->assertSessionHasErrors('is_active');

        $this->assertTrue($admin->fresh()->is_active);
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
