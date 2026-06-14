<?php

namespace Tests\Feature;

use App\Models\AffiliateClick;
use App\Models\AffiliateLink;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliateClickAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_recent_click_analytics(): void
    {
        $editor = $this->userWithRole('editor');
        $link = AffiliateLink::create([
            'title' => 'Tracked Tool',
            'url' => 'https://example.com/tracked',
            'slug' => 'tracked-tool',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'clicks' => 4,
            'is_active' => true,
        ]);

        AffiliateClick::create([
            'affiliate_link_id' => $link->id,
            'clicked_at' => now()->subDays(2),
        ]);
        AffiliateClick::create([
            'affiliate_link_id' => $link->id,
            'clicked_at' => now()->subDays(10),
        ]);
        AffiliateClick::create([
            'affiliate_link_id' => $link->id,
            'clicked_at' => now()->subDays(40),
        ]);

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index'))
            ->assertOk()
            ->assertSee('Click 7 ngày gần nhất')
            ->assertSee('7 ngày: 1')
            ->assertSee('30 ngày: 2');
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create(['name' => $roleName]);
        $permission = Permission::firstOrCreate(['name' => 'view-posts']);
        $role->permissions()->syncWithoutDetaching($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
