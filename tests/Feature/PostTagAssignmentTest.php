<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTagAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_attach_tags_when_creating_post(): void
    {
        $author = $this->userWithRole('author', ['create-post', 'edit-post']);
        $category = Category::create(['slug' => 'guides']);
        $tag = Tag::create(['slug' => 'laravel']);
        $tag->setTranslation('name', 'Laravel', app()->getLocale());

        $this->actingAs($author)
            ->post(route('creator.posts.store'), [
                'category_id' => $category->id,
                'title' => 'Tagged article',
                'content' => 'Tagged content',
                'status' => 'draft',
                'tag_ids' => [$tag->id],
            ])
            ->assertRedirect(route('creator.posts.index'));

        $post = Post::where('slug', 'tagged-article')->firstOrFail();

        $this->assertDatabaseHas('post_tag', [
            'post_id' => $post->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_creator_can_update_post_tags(): void
    {
        $author = $this->userWithRole('author', ['edit-post']);
        $category = Category::create(['slug' => 'guides']);
        $oldTag = Tag::create(['slug' => 'old']);
        $newTag = Tag::create(['slug' => 'new']);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'editable-post',
            'status' => 'draft',
        ]);
        $post->setTranslation('title', 'Editable post', app()->getLocale());
        $post->setTranslation('content', 'Editable content', app()->getLocale());
        $post->tags()->attach($oldTag);

        $this->actingAs($author)
            ->put(route('creator.posts.update', $post->slug), [
                'category_id' => $category->id,
                'title' => 'Editable post',
                'content' => 'Updated content',
                'status' => 'draft',
                'tag_ids' => [$newTag->id],
            ])
            ->assertRedirect(route('creator.posts.index'));

        $this->assertDatabaseMissing('post_tag', [
            'post_id' => $post->id,
            'tag_id' => $oldTag->id,
        ]);
        $this->assertDatabaseHas('post_tag', [
            'post_id' => $post->id,
            'tag_id' => $newTag->id,
        ]);
    }

    public function test_public_post_shows_tags(): void
    {
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'guides']);
        $tag = Tag::create(['slug' => 'seo']);
        $tag->setTranslation('name', 'SEO', app()->getLocale());
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'published-tagged-post',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $post->setTranslation('title', 'Published tagged post', app()->getLocale());
        $post->setTranslation('content', 'Published content', app()->getLocale());
        $post->tags()->attach($tag);

        $this->get(route('posts.show', $post->slug))
            ->assertOk()
            ->assertSee('SEO');
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
