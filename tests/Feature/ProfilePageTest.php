<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_with_roles_posts_and_activity(): void
    {
        $role = Role::create(['name' => 'author']);
        $user = User::factory()->create([
            'name' => 'Profile User',
            'email' => 'profile@example.com',
        ]);
        $user->assignRole($role);
        $category = Category::create(['slug' => 'guides']);
        $post = Post::create([
            'category_id' => $category->id,
            'author_id' => $user->id,
            'slug' => 'profile-post',
            'status' => 'draft',
        ]);
        $post->setTranslation('title', 'Profile post', app()->getLocale());

        AuditLog::create([
            'user_id' => $user->id,
            'model_type' => Post::class,
            'model_id' => $post->id,
            'action' => 'post.updated',
            'description' => 'Updated profile post.',
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Hồ sơ của tôi')
            ->assertSee('Profile User')
            ->assertSee('profile@example.com')
            ->assertSee('author')
            ->assertSee('Profile post')
            ->assertSee('post.updated')
            ->assertSee('Updated profile post.');
    }

    public function test_guest_cannot_view_profile(): void
    {
        $this->get(route('profile.show'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'phone_number' => null,
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'New Name',
                'phone_number' => '0909123456',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'phone_number' => '0909123456',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'profile.updated',
        ]);
    }

    public function test_user_can_update_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($user)
            ->put(route('profile.password'), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'profile.password_updated',
        ]);
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($user)
            ->from(route('profile.show'))
            ->put(route('profile.password'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
