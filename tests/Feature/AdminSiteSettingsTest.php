<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_update_site_settings(): void
    {
        $admin = $this->userWithRole('admin', ['manage-users']);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Site settings');

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name' => 'Review Lab',
                'site_tagline' => 'Chọn sản phẩm tốt hơn',
                'default_meta_description' => 'Review Lab giúp bạn chọn sản phẩm affiliate đáng tin cậy.',
                'default_og_image' => 'https://example.com/og.jpg',
                'facebook_url' => 'https://facebook.com/reviewlab',
                'youtube_url' => 'https://youtube.com/@reviewlab',
                'tiktok_url' => 'https://www.tiktok.com/@reviewlab',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => 'Review Lab',
        ]);
        $this->assertDatabaseHas('site_settings', [
            'key' => 'default_meta_description',
            'value' => 'Review Lab giúp bạn chọn sản phẩm affiliate đáng tin cậy.',
        ]);
    }

    public function test_user_without_manage_users_cannot_manage_site_settings(): void
    {
        $editor = $this->userWithRole('editor', ['view-posts']);

        $this->actingAs($editor)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->put(route('admin.settings.update'), ['site_name' => 'Blocked'])
            ->assertForbidden();
    }

    public function test_public_layout_uses_configured_site_settings(): void
    {
        SiteSetting::setMany([
            'site_name' => 'Review Lab',
            'default_meta_description' => 'Default SEO description',
            'default_og_image' => 'https://example.com/og.jpg',
            'facebook_url' => 'https://facebook.com/reviewlab',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Bài viết mới nhất | Review Lab</title>', false)
            ->assertSee('Review Lab')
            ->assertSee('Default SEO description')
            ->assertSee('https://example.com/og.jpg')
            ->assertSee('https://facebook.com/reviewlab');
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
