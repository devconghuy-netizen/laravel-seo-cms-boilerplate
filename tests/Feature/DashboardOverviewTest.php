<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\AffiliateConversion;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_sees_personal_dashboard_only(): void
    {
        $author = $this->userWithRole('author');
        $category = Category::create(['slug' => 'guides']);
        $post = $this->postFor($author, $category, 'author-draft', 'Author draft', 'draft');

        $this->actingAs($author)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bài của tôi')
            ->assertSee('Author draft')
            ->assertDontSee('Bài cần xử lý')
            ->assertDontSee('Affiliate performance');
    }

    public function test_editor_sees_admin_overview_and_affiliate_performance(): void
    {
        $editor = $this->userWithRole('editor');
        $author = $this->userWithRole('author');
        $category = Category::create(['slug' => 'reviews']);
        $draft = $this->postFor($author, $category, 'review-draft', 'Review draft', 'draft');
        $this->postFor($author, $category, 'published-review', 'Published review', 'published');

        AffiliateLink::create([
            'post_id' => $draft->id,
            'title' => 'Demo affiliate',
            'url' => 'https://example.com/demo',
            'slug' => 'demo-affiliate',
            'affiliate_program' => 'Demo',
            'commission_rate' => 20,
            'type' => 'product',
            'clicks' => 12,
            'conversions' => 3,
            'earnings' => 0.60,
            'is_active' => true,
        ]);

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bài cần xử lý')
            ->assertSee('Review draft')
            ->assertSee('Affiliate performance')
            ->assertSee('Demo affiliate')
            ->assertSee('$0.60');
    }

    public function test_editor_sees_dashboard_signals_without_audit_log_list(): void
    {
        $editor = $this->userWithRole('editor');
        $link = AffiliateLink::create([
            'title' => 'Signal affiliate',
            'url' => 'https://example.com/signal',
            'slug' => 'signal-affiliate',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);

        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 1.25,
            'converted_at' => now(),
        ]);
        AuditLog::create([
            'user_id' => $editor->id,
            'model_type' => AffiliateLink::class,
            'model_id' => 0,
            'action' => 'affiliate.imported',
            'description' => 'Imported affiliate links from CSV.',
        ]);

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Conversion 7')
            ->assertSee('Import affiliate 7')
            ->assertSee('Conversion mới')
            ->assertSee('Signal affiliate')
            ->assertDontSee('Audit log mới');
    }

    public function test_admin_with_manage_users_sees_recent_audit_logs(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);
        AuditLog::create([
            'user_id' => $admin->id,
            'model_type' => User::class,
            'model_id' => $admin->id,
            'action' => 'user.updated',
            'description' => 'Updated user roles or account status.',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Audit log mới')
            ->assertSee('Activity timeline')
            ->assertSee('user.updated')
            ->assertSee('Updated user roles or account status.');
    }

    public function test_editor_without_manage_users_does_not_see_activity_timeline(): void
    {
        $editor = $this->userWithRole('editor');

        AuditLog::create([
            'user_id' => $editor->id,
            'model_type' => User::class,
            'model_id' => $editor->id,
            'action' => 'user.updated',
            'description' => 'Updated user roles or account status.',
        ]);

        $this->actingAs($editor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Activity timeline')
            ->assertDontSee('Updated user roles or account status.');
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

    private function postFor(User $author, Category $category, string $slug, string $title, string $status): Post
    {
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
            'views_count' => 25,
        ]);

        $post->setTranslation('title', $title, app()->getLocale());

        return $post;
    }
}
