<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_admin_posts_index(): void
    {
        $editor = $this->userWithRole('editor', ['edit-post', 'publish-post']);

        $this->actingAs($editor)
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Quản lý bài viết');
    }

    public function test_author_cannot_open_admin_posts_index(): void
    {
        $author = $this->userWithRole('author', ['create-post', 'edit-post']);

        $this->actingAs($author)
            ->get(route('admin.posts.index'))
            ->assertForbidden();
    }

    public function test_editor_can_publish_another_authors_post(): void
    {
        $author = $this->userWithRole('author', ['create-post']);
        $editor = $this->userWithRole('editor', ['edit-post', 'publish-post']);
        $category = Category::create(['slug' => 'reviews']);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'pending-review',
            'status' => 'draft',
        ]);

        $this->actingAs($editor)
            ->post(route('admin.posts.publish', $post->slug))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'published',
        ]);
        $this->assertNotNull($post->fresh()->published_at);
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
