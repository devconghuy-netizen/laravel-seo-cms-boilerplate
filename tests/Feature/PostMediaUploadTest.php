<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_upload_featured_and_og_images_when_creating_post(): void
    {
        Storage::fake('public');

        $author = $this->userWithRole('author', ['create-post', 'edit-post']);
        $category = Category::create(['slug' => 'media']);

        $this->actingAs($author)
            ->post(route('creator.posts.store'), [
                'category_id' => $category->id,
                'title' => 'Media article',
                'content' => 'Media content',
                'status' => 'draft',
                'featured_image_file' => UploadedFile::fake()->image('featured.jpg', 1200, 630),
                'seo_og_image_file' => UploadedFile::fake()->image('og.jpg', 1200, 630),
                'seo_title' => 'Media SEO title',
            ])
            ->assertRedirect(route('creator.posts.index'));

        $post = Post::where('slug', 'media-article')->firstOrFail();
        $media = Media::orderBy('id')->get();
        $featured = $media->firstWhere('original_filename', 'featured.jpg');
        $og = $media->firstWhere('original_filename', 'og.jpg');

        $this->assertCount(2, $media);
        $this->assertSame('/storage/'.$featured->path, $post->featured_image);
        $this->assertSame('/storage/'.$og->path, $post->seoMeta->og_image);
        $this->assertSame('Media SEO title', $post->seoMeta->title);

        Storage::disk('public')->assertExists($featured->path);
        Storage::disk('public')->assertExists($og->path);
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
