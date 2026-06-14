<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorPostAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_create_draft_post(): void
    {
        $author = $this->userWithRole('author', ['create-post', 'edit-post', 'view-categories']);
        $category = Category::create(['slug' => 'news']);

        $response = $this->actingAs($author)->post(route('creator.posts.store'), [
            'category_id' => $category->id,
            'title' => 'Draft article',
            'excerpt' => 'Short summary',
            'content' => 'Draft content',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('creator.posts.index'));
        $this->assertDatabaseHas('posts', [
            'slug' => 'draft-article',
            'author_id' => $author->id,
            'status' => 'draft',
        ]);
    }

    public function test_author_cannot_publish_without_publish_permission(): void
    {
        $author = $this->userWithRole('author', ['create-post', 'edit-post', 'view-categories']);
        $category = Category::create(['slug' => 'news']);

        $response = $this->actingAs($author)->post(route('creator.posts.store'), [
            'category_id' => $category->id,
            'title' => 'Published article',
            'content' => 'Published content',
            'status' => 'published',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['slug' => 'published-article']);
    }

    public function test_user_without_create_permission_cannot_open_create_form(): void
    {
        $user = $this->userWithRole('user', ['view-posts']);

        $this->actingAs($user)
            ->get(route('creator.posts.create'))
            ->assertForbidden();
    }

    public function test_editor_can_delete_another_authors_post(): void
    {
        $author = $this->userWithRole('author', ['create-post', 'edit-post']);
        $editor = $this->userWithRole('editor', ['edit-post', 'delete-post', 'publish-post']);
        $category = Category::create(['slug' => 'news']);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'other-author-post',
            'status' => 'draft',
        ]);

        $this->actingAs($editor)
            ->delete(route('creator.posts.destroy', $post->slug))
            ->assertRedirect(route('creator.posts.index'));

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
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
