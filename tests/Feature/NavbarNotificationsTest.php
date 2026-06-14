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

class NavbarNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_does_not_see_admin_notifications(): void
    {
        $author = $this->userWithRole('author');

        $this->actingAs($author)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Notifications');
    }

    public function test_editor_sees_content_and_conversion_notifications(): void
    {
        $editor = $this->userWithRole('editor');
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);
        Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'draft-needs-review',
            'status' => 'draft',
        ]);
        $link = AffiliateLink::create([
            'title' => 'Notify Tool',
            'url' => 'https://example.com/notify',
            'slug' => 'notify-tool',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);
        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 0.25,
            'converted_at' => now(),
        ]);

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('Bản nháp cần duyệt')
            ->assertSee('Conversion 7 ngày')
            ->assertDontSee(route('admin.audit-logs.index'), false);
    }

    public function test_admin_with_manage_users_sees_audit_notifications(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);
        AuditLog::create([
            'user_id' => $admin->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
            'action' => 'user.updated',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('Audit 24h');
    }

    public function test_user_can_mark_single_notification_as_read(): void
    {
        $editor = $this->userWithRole('editor');
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);
        Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'draft-needs-review',
            'status' => 'draft',
        ]);

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('text-bg-danger', false);

        $this->actingAs($editor)
            ->post(route('notifications.read', 'draft_posts'))
            ->assertRedirect();

        $this->assertDatabaseHas('notification_reads', [
            'user_id' => $editor->id,
            'notification_key' => 'draft_posts',
        ]);

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('text-bg-danger', false);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);
        AuditLog::create([
            'user_id' => $admin->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
            'action' => 'user.updated',
        ]);

        $this->actingAs($admin)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        foreach (['draft_posts', 'conversions_7_days', 'audit_logs_24_hours'] as $key) {
            $this->assertDatabaseHas('notification_reads', [
                'user_id' => $admin->id,
                'notification_key' => $key,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('text-bg-danger', false);
    }

    public function test_invalid_notification_key_returns_404(): void
    {
        $editor = $this->userWithRole('editor');

        $this->actingAs($editor)
            ->post(route('notifications.read', 'unknown-key'))
            ->assertNotFound();
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
