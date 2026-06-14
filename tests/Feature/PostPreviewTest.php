<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PostPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_preview_own_draft_with_signed_url(): void
    {
        $author = $this->userWithRole('author', ['edit-post']);
        $post = $this->draftPostFor($author, 'private-draft');

        $this->actingAs($author)
            ->get($this->previewUrl($post))
            ->assertOk()
            ->assertSee('Preview mode')
            ->assertSee('Private Draft');
    }

    public function test_public_post_route_still_hides_drafts(): void
    {
        $author = $this->userWithRole('author', ['edit-post']);
        $post = $this->draftPostFor($author, 'hidden-draft');

        $this->get(route('posts.show', $post->slug))->assertNotFound();
    }

    public function test_other_author_cannot_preview_someone_elses_draft(): void
    {
        $author = $this->userWithRole('author', ['edit-post']);
        $otherAuthor = $this->userWithRole('other-author', ['edit-post']);
        $post = $this->draftPostFor($author, 'private-draft');

        $this->actingAs($otherAuthor)
            ->get($this->previewUrl($post))
            ->assertForbidden();
    }

    public function test_preview_requires_signed_url(): void
    {
        $author = $this->userWithRole('author', ['edit-post']);
        $post = $this->draftPostFor($author, 'private-draft');

        $this->actingAs($author)
            ->get(route('posts.preview', $post->slug))
            ->assertForbidden();
    }

    private function previewUrl(Post $post): string
    {
        return URL::temporarySignedRoute('posts.preview', now()->addMinutes(30), [
            'post' => $post->slug,
        ]);
    }

    private function draftPostFor(User $user, string $slug): Post
    {
        $category = Category::create(['slug' => 'reviews', 'is_active' => true]);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $user->id,
            'slug' => $slug,
            'status' => 'draft',
        ]);

        $post->setTranslation('title', 'Private Draft', app()->getLocale());
        $post->setTranslation('content', 'Draft content that should stay private.', app()->getLocale());

        return $post;
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
