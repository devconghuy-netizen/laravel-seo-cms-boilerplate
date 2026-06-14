<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_actions_create_audit_logs(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users', 'publish-post', 'view-posts']);
        $editorRole = Role::create(['name' => 'editor']);
        $user = User::factory()->create(['is_active' => true]);
        $category = Category::create(['slug' => 'reviews']);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $user->id,
            'slug' => 'draft-post',
            'status' => 'draft',
        ]);
        $affiliateLink = AffiliateLink::create([
            'title' => 'Demo Tool',
            'url' => 'https://example.com/demo',
            'slug' => 'demo-tool',
            'affiliate_program' => 'Demo',
            'commission_rate' => 25,
            'type' => 'product',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.posts.publish', $post->slug));
        $this->actingAs($admin)->put(route('admin.users.update', $user), ['role_ids' => [$editorRole->id]]);
        $this->actingAs($admin)->post(route('admin.affiliate-links.conversion', $affiliateLink->slug));
        $this->actingAs($admin)->post(route('admin.affiliate-links.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent(
                'links.csv',
                "title,url,affiliate_program,type,slug\nImported Tool,https://example.com/imported,Demo,product,imported-tool"
            ),
        ]);

        $this->assertDatabaseHas('audit_logs', ['action' => 'post.published', 'model_id' => $post->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.updated', 'model_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'affiliate.conversion_recorded', 'model_id' => $affiliateLink->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'affiliate.imported']);
    }

    public function test_admin_can_view_and_filter_audit_logs(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index', ['action' => 'post.published']))
            ->assertOk()
            ->assertSee('Audit logs');
    }

    public function test_user_without_manage_users_cannot_view_audit_logs(): void
    {
        $editor = $this->userWithRole('editor', ['view-posts']);

        $this->actingAs($editor)
            ->get(route('admin.audit-logs.index'))
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
