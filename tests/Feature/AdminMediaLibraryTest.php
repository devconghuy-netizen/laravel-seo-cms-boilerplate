<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_open_media_library(): void
    {
        $editor = $this->userWithRole('editor');
        $uploader = User::factory()->create(['name' => 'Uploader']);
        Media::create([
            'user_id' => $uploader->id,
            'name' => 'sample',
            'original_filename' => 'sample.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'disk' => 'public',
            'path' => 'media/sample.jpg',
            'media_type' => 'image',
        ]);

        $this->actingAs($editor)
            ->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('sample.jpg');
    }

    public function test_author_cannot_open_media_library(): void
    {
        $author = $this->userWithRole('author');

        $this->actingAs($author)
            ->get(route('admin.media.index'))
            ->assertForbidden();
    }

    public function test_editor_can_delete_media_file(): void
    {
        Storage::fake('public');

        $editor = $this->userWithRole('editor');
        $uploader = User::factory()->create();
        Storage::disk('public')->put('media/delete-me.jpg', 'fake-image');
        $media = Media::create([
            'user_id' => $uploader->id,
            'name' => 'delete-me',
            'original_filename' => 'delete-me.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'disk' => 'public',
            'path' => 'media/delete-me.jpg',
            'media_type' => 'image',
        ]);

        $this->actingAs($editor)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('admin.media.index'));

        $this->assertSoftDeleted('media', ['id' => $media->id]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create(['name' => $roleName]);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
