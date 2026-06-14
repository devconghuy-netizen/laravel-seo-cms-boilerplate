<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_save_seo_meta_when_creating_post(): void
    {
        $author = $this->userWithRole('author', ['create-post', 'edit-post']);
        $category = Category::create(['slug' => 'seo']);

        $this->actingAs($author)
            ->post(route('creator.posts.store'), [
                'category_id' => $category->id,
                'title' => 'SEO article',
                'content' => 'SEO content',
                'status' => 'draft',
                'seo_title' => 'Custom SEO title',
                'seo_description' => 'Custom SEO description',
                'seo_keywords' => 'seo,laravel',
                'seo_canonical_url' => 'https://example.com/seo-article',
                'seo_og_image' => 'https://example.com/og.jpg',
                'seo_twitter_card' => 'summary_large_image',
                'seo_index' => '1',
                'seo_follow' => '1',
            ])
            ->assertRedirect(route('creator.posts.index'));

        $post = Post::where('slug', 'seo-article')->firstOrFail();

        $this->assertDatabaseHas('seo_metas', [
            'seoable_type' => Post::class,
            'seoable_id' => $post->id,
            'title' => 'Custom SEO title',
            'description' => 'Custom SEO description',
            'canonical_url' => 'https://example.com/seo-article',
            'og_image' => 'https://example.com/og.jpg',
            'index' => true,
            'follow' => true,
        ]);
    }

    public function test_public_post_renders_seo_meta_tags(): void
    {
        $author = User::factory()->create(['name' => 'Author']);
        $category = Category::create(['slug' => 'reviews']);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'review-post',
            'status' => 'published',
            'published_at' => now(),
            'featured_image' => 'https://example.com/featured.jpg',
        ]);
        $post->setTranslation('title', 'Review post', app()->getLocale());
        $post->setTranslation('content', 'Review content', app()->getLocale());
        $post->seoMeta()->create([
            'locale' => app()->getLocale(),
            'title' => 'SEO review title',
            'description' => 'SEO review description',
            'canonical_url' => 'https://example.com/review-post',
            'og_image' => 'https://example.com/og-review.jpg',
            'og_type' => 'article',
            'twitter_card' => 'summary_large_image',
            'index' => true,
            'follow' => true,
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('<title>SEO review title | AffiPress</title>', false);
        $response->assertSee('<meta name="description" content="SEO review description">', false);
        $response->assertSee('<link rel="canonical" href="https://example.com/review-post">', false);
        $response->assertSee('<meta property="og:image" content="https://example.com/og-review.jpg">', false);
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
