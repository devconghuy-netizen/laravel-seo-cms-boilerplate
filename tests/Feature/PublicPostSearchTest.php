<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_can_search_published_posts_by_title(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'guides']);
        $matching = $this->postFor($author, $category, 'laravel-guide', 'Laravel guide', 'published');
        $other = $this->postFor($author, $category, 'seo-guide', 'SEO guide', 'published');

        $response = $this->get(route('home', ['q' => 'Laravel']));

        $response->assertOk();
        $response->assertSee('Kết quả tìm kiếm');
        $response->assertSee($matching->getTranslation('title', app()->getLocale()));
        $response->assertDontSee($other->getTranslation('title', app()->getLocale()));
    }

    public function test_search_does_not_show_draft_posts(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'guides']);
        $draft = $this->postFor($author, $category, 'private-laravel-note', 'Private Laravel note', 'draft');

        $response = $this->get(route('home', ['q' => 'Laravel']));

        $response->assertOk();
        $response->assertDontSee($draft->getTranslation('title', app()->getLocale()));
        $response->assertSee('Không tìm thấy bài viết phù hợp.');
    }

    public function test_search_can_match_post_slug(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'guides']);
        $post = $this->postFor($author, $category, 'special-slug-match', 'Ordinary title', 'published');

        $response = $this->get(route('home', ['q' => 'special-slug']));

        $response->assertOk();
        $response->assertSee($post->getTranslation('title', app()->getLocale()));
    }

    private function postFor(User $author, Category $category, string $slug, string $title, string $status): Post
    {
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
        $post->setTranslation('title', $title, app()->getLocale());
        $post->setTranslation('content', "{$title} content", app()->getLocale());

        return $post;
    }
}
