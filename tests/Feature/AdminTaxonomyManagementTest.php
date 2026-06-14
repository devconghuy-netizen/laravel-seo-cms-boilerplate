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

class AdminTaxonomyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_category(): void
    {
        $editor = $this->userWithRole('editor', ['view-categories', 'create-category']);

        $this->actingAs($editor)
            ->post(route('admin.categories.store'), [
                'name' => 'Buying Guides',
                'description' => 'Product buying guides',
                'sort_order' => 5,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'slug' => 'buying-guides',
            'description' => 'Product buying guides',
            'sort_order' => 5,
            'is_active' => true,
        ]);
    }

    public function test_author_cannot_open_category_admin(): void
    {
        $author = $this->userWithRole('author', ['view-categories']);

        $this->actingAs($author)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_category_with_posts_is_not_deleted(): void
    {
        $editor = $this->userWithRole('editor', ['view-categories', 'delete-category']);
        $author = User::factory()->create();
        $category = Category::create(['slug' => 'reviews']);
        Post::create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'review-post',
            'status' => 'draft',
        ]);

        $this->actingAs($editor)
            ->delete(route('admin.categories.destroy', $category->slug))
            ->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    public function test_editor_can_create_and_delete_tag(): void
    {
        $editor = $this->userWithRole('editor', ['view-tags', 'create-tag', 'delete-tag']);

        $this->actingAs($editor)
            ->post(route('admin.tags.store'), [
                'name' => 'Deals',
                'color' => '#198754',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.tags.index'));

        $tag = Tag::where('slug', 'deals')->firstOrFail();

        $this->actingAs($editor)
            ->delete(route('admin.tags.destroy', $tag->slug))
            ->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
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
