<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_search_posts_by_translated_title(): void
    {
        $editor = $this->userWithRole('editor');
        $author = User::factory()->create(['name' => 'Author One']);
        $category = Category::create(['slug' => 'reviews']);
        $matching = $this->postFor($author, $category, 'laravel-guide', 'Laravel buying guide', 'draft');
        $other = $this->postFor($author, $category, 'seo-guide', 'SEO guide', 'draft');

        $this->actingAs($editor)
            ->get(route('admin.posts.index', ['q' => 'Laravel']))
            ->assertOk()
            ->assertSee($matching->getTranslation('title', app()->getLocale()))
            ->assertDontSee($other->getTranslation('title', app()->getLocale()));
    }

    public function test_editor_can_filter_posts_by_status_category_and_author(): void
    {
        $editor = $this->userWithRole('editor');
        $targetAuthor = User::factory()->create(['name' => 'Target Author']);
        $otherAuthor = User::factory()->create(['name' => 'Other Author']);
        $targetCategory = Category::create(['slug' => 'target-category']);
        $otherCategory = Category::create(['slug' => 'other-category']);
        $matching = $this->postFor($targetAuthor, $targetCategory, 'target-post', 'Target post', 'published');
        $wrongAuthor = $this->postFor($otherAuthor, $targetCategory, 'wrong-author', 'Wrong author', 'published');
        $wrongCategory = $this->postFor($targetAuthor, $otherCategory, 'wrong-category', 'Wrong category', 'published');
        $wrongStatus = $this->postFor($targetAuthor, $targetCategory, 'wrong-status', 'Wrong status', 'draft');

        $this->actingAs($editor)
            ->get(route('admin.posts.index', [
                'status' => 'published',
                'category_id' => $targetCategory->id,
                'author_id' => $targetAuthor->id,
            ]))
            ->assertOk()
            ->assertSee($matching->getTranslation('title', app()->getLocale()))
            ->assertDontSee($wrongAuthor->getTranslation('title', app()->getLocale()))
            ->assertDontSee($wrongCategory->getTranslation('title', app()->getLocale()))
            ->assertDontSee($wrongStatus->getTranslation('title', app()->getLocale()));
    }

    public function test_editor_can_sort_posts_by_views(): void
    {
        $editor = $this->userWithRole('editor');
        $author = User::factory()->create(['name' => 'Author One']);
        $category = Category::create(['slug' => 'reviews']);
        $low = $this->postFor($author, $category, 'low-views', 'Low views', 'published', 5);
        $high = $this->postFor($author, $category, 'high-views', 'High views', 'published', 150);

        $response = $this->actingAs($editor)
            ->get(route('admin.posts.index', ['sort' => 'views']))
            ->assertOk();

        $response->assertSeeInOrder([
            $high->getTranslation('title', app()->getLocale()),
            $low->getTranslation('title', app()->getLocale()),
        ]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create(['name' => $roleName]);

        foreach (['edit-post', 'publish-post', 'delete-post'] as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->permissions()->syncWithoutDetaching($permission);
        }

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function postFor(
        User $author,
        Category $category,
        string $slug,
        string $title,
        string $status,
        int $views = 0
    ): Post {
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
            'views_count' => $views,
        ]);

        $post->setTranslation('title', $title, app()->getLocale());

        return $post;
    }
}
