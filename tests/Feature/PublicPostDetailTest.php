<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_post_view_increments_views_count(): void
    {
        $post = $this->postFor('main-post', 'Main post');

        $this->get(route('posts.show', $post->slug))->assertOk();

        $this->assertSame(1, $post->fresh()->views_count);
    }

    public function test_public_post_shows_active_affiliate_links(): void
    {
        $post = $this->postFor('main-post', 'Main post');
        $activeLink = AffiliateLink::create([
            'post_id' => $post->id,
            'title' => 'Recommended product',
            'description' => 'Best value option',
            'url' => 'https://example.com/recommended',
            'slug' => 'recommended-product',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);
        AffiliateLink::create([
            'post_id' => $post->id,
            'title' => 'Hidden product',
            'url' => 'https://example.com/hidden',
            'slug' => 'hidden-product',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => false,
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('Recommended product');
        $response->assertSee(route('affiliate.redirect', $activeLink->slug));
        $response->assertDontSee('Hidden product');
    }

    public function test_public_post_shows_related_posts_from_same_category(): void
    {
        $category = Category::create(['slug' => 'guides']);
        $author = User::factory()->create();
        $post = $this->postFor('main-post', 'Main post', $category, $author);
        $related = $this->postFor('related-post', 'Related post', $category, $author);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('Bài viết liên quan');
        $response->assertSee($related->getTranslation('title', app()->getLocale()));
        $response->assertSee(route('posts.show', $related->slug));
    }

    private function postFor(
        string $slug,
        string $title,
        ?Category $category = null,
        ?User $author = null
    ): Post {
        $category ??= Category::create(['slug' => 'guides']);
        $author ??= User::factory()->create();

        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => $slug,
            'status' => 'published',
            'published_at' => now(),
        ]);
        $post->setTranslation('title', $title, app()->getLocale());
        $post->setTranslation('content', "{$title} content", app()->getLocale());

        return $post;
    }
}
