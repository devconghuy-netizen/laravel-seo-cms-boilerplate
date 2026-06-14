<?php

namespace Tests\Feature;

use App\Models\AffiliateConversion;
use App\Models\AffiliateLink;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_notification_center_without_audit_section(): void
    {
        $editor = $this->userWithRole('editor');
        $author = User::factory()->create(['name' => 'Draft Author']);
        $category = Category::create(['slug' => 'reviews']);
        Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'pending-draft',
            'status' => 'draft',
        ]);
        $link = AffiliateLink::create([
            'title' => 'Center Tool',
            'url' => 'https://example.com/center',
            'slug' => 'center-tool',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);
        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 2.50,
            'converted_at' => now(),
        ]);

        $this->actingAs($editor)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notification center')
            ->assertSee('pending-draft')
            ->assertSee('Center Tool')
            ->assertDontSee('Audit 24h');
    }

    public function test_admin_can_view_audit_section_in_notification_center(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);
        AuditLog::create([
            'user_id' => $admin->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
            'action' => 'user.updated',
            'description' => 'Updated user roles.',
        ]);

        $this->actingAs($admin)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Audit 24h')
            ->assertSee('user.updated');
    }

    public function test_author_cannot_view_notification_center(): void
    {
        $author = $this->userWithRole('author');

        $this->actingAs($author)
            ->get(route('notifications.index'))
            ->assertForbidden();
    }

    public function test_mark_read_from_notification_center_updates_read_state(): void
    {
        $editor = $this->userWithRole('editor');
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);
        Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'pending-draft',
            'status' => 'draft',
        ]);

        $this->actingAs($editor)
            ->post(route('notifications.read', 'draft_posts'))
            ->assertRedirect();

        $this->assertDatabaseHas('notification_reads', [
            'user_id' => $editor->id,
            'notification_key' => 'draft_posts',
        ]);

        $this->actingAs($editor)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertDontSee('badge text-bg-danger ms-1', false);
    }

    private function userWithRole(string $roleName, array $permissionNames = []): User
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
