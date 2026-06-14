<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTaxonomyPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_shows_only_published_posts(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);
        $category->setTranslation('name', 'Reviews', app()->getLocale());
        $published = $this->postFor($author, $category, 'published-post', 'Published post', 'published');
        $draft = $this->postFor($author, $category, 'draft-post', 'Draft post', 'draft');

        $response = $this->get(route('categories.show', $category->slug));

        $response->assertOk();
        $response->assertSee('Reviews');
        $response->assertSee($published->getTranslation('title', app()->getLocale()));
        $response->assertDontSee($draft->getTranslation('title', app()->getLocale()));
    }

    public function test_tag_page_shows_only_published_posts(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'guides']);
        $tag = Tag::create(['slug' => 'seo']);
        $tag->setTranslation('name', 'SEO', app()->getLocale());
        $published = $this->postFor($author, $category, 'seo-post', 'SEO post', 'published');
        $draft = $this->postFor($author, $category, 'seo-draft', 'SEO draft', 'draft');
        $published->tags()->attach($tag);
        $draft->tags()->attach($tag);

        $response = $this->get(route('tags.show', $tag->slug));

        $response->assertOk();
        $response->assertSee('SEO');
        $response->assertSee($published->getTranslation('title', app()->getLocale()));
        $response->assertDontSee($draft->getTranslation('title', app()->getLocale()));
    }

    public function test_inactive_category_and_tag_return_404(): void
    {
        $category = Category::create(['slug' => 'hidden', 'is_active' => false]);
        $tag = Tag::create(['slug' => 'hidden-tag', 'is_active' => false]);

        $this->get(route('categories.show', $category->slug))->assertNotFound();
        $this->get(route('tags.show', $tag->slug))->assertNotFound();
    }

    public function test_post_show_links_to_category_and_tags(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);
        $tag = Tag::create(['slug' => 'seo']);
        $post = $this->postFor($author, $category, 'linked-post', 'Linked post', 'published');
        $post->tags()->attach($tag);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee(route('categories.show', $category->slug), false);
        $response->assertSee(route('tags.show', $tag->slug), false);
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
