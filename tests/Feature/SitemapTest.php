<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_public_content_only(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews', 'is_active' => true]);
        $inactiveCategory = Category::create(['slug' => 'hidden-category', 'is_active' => false]);
        $tag = Tag::create(['slug' => 'seo', 'is_active' => true]);
        $inactiveTag = Tag::create(['slug' => 'hidden-tag', 'is_active' => false]);
        $publishedPost = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'published-post',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'draft-post',
            'status' => 'draft',
        ]);
        $activeProduct = AffiliateLink::create([
            'title' => 'Active Product',
            'url' => 'https://example.com/active',
            'slug' => 'active-product',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);
        AffiliateLink::create([
            'title' => 'Inactive Product',
            'url' => 'https://example.com/inactive',
            'slug' => 'inactive-product',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => false,
        ]);

        $this->get(route('sitemap.index'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('products.index'), false)
            ->assertSee(route('posts.show', $publishedPost), false)
            ->assertSee(route('products.show', $activeProduct), false)
            ->assertSee(route('categories.show', $category), false)
            ->assertSee(route('tags.show', $tag), false)
            ->assertDontSee('draft-post')
            ->assertDontSee('inactive-product')
            ->assertDontSee(route('categories.show', $inactiveCategory), false)
            ->assertDontSee(route('tags.show', $inactiveTag), false);
    }

    public function test_robots_txt_points_to_sitemap_and_blocks_private_areas(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /creator')
            ->assertSee('Sitemap: '.route('sitemap.index'));
    }
}
