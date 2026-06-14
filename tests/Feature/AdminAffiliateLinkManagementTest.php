<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliateLinkManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_affiliate_link(): void
    {
        $editor = $this->userWithRole('editor');
        $post = $this->publishedPost();

        $this->actingAs($editor)
            ->post(route('admin.affiliate-links.store'), [
                'post_id' => $post->id,
                'title' => 'SEO Tool',
                'description' => 'Useful SEO tool',
                'url' => 'https://example.com/seo-tool',
                'affiliate_program' => 'Demo Program',
                'product_id' => 'SEO-1',
                'commission_rate' => '12.5',
                'type' => 'product',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.affiliate-links.index'));

        $this->assertDatabaseHas('affiliate_links', [
            'post_id' => $post->id,
            'title' => 'SEO Tool',
            'slug' => 'seo-tool',
            'affiliate_program' => 'Demo Program',
            'is_active' => true,
        ]);
    }

    public function test_editor_can_update_affiliate_link(): void
    {
        $editor = $this->userWithRole('editor');
        $link = $this->affiliateLink();

        $this->actingAs($editor)
            ->put(route('admin.affiliate-links.update', $link->slug), [
                'title' => 'Updated Tool',
                'url' => 'https://example.com/updated',
                'affiliate_program' => 'Updated Program',
                'type' => 'service',
            ])
            ->assertRedirect(route('admin.affiliate-links.index'));

        $this->assertDatabaseHas('affiliate_links', [
            'id' => $link->id,
            'title' => 'Updated Tool',
            'slug' => 'updated-tool',
            'type' => 'service',
            'is_active' => false,
        ]);
    }

    public function test_editor_can_delete_affiliate_link(): void
    {
        $editor = $this->userWithRole('editor');
        $link = $this->affiliateLink();

        $this->actingAs($editor)
            ->delete(route('admin.affiliate-links.destroy', $link->slug))
            ->assertRedirect(route('admin.affiliate-links.index'));

        $this->assertSoftDeleted('affiliate_links', ['id' => $link->id]);
    }

    public function test_author_cannot_open_affiliate_admin(): void
    {
        $author = $this->userWithRole('author');

        $this->actingAs($author)
            ->get(route('admin.affiliate-links.index'))
            ->assertForbidden();
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create(['name' => $roleName]);
        $permission = Permission::firstOrCreate(['name' => 'view-posts']);
        $role->permissions()->syncWithoutDetaching($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function publishedPost(): Post
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);

        return Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'review-post',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    private function affiliateLink(): AffiliateLink
    {
        return AffiliateLink::create([
            'title' => 'Demo Tool',
            'url' => 'https://example.com/demo',
            'slug' => 'demo-tool',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);
    }
}
