<?php

namespace Tests\Feature;

use App\Models\AffiliateConversion;
use App\Models\AffiliateLink;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliateConversionAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_recent_conversion_analytics(): void
    {
        $editor = $this->userWithRole('editor');
        $link = AffiliateLink::create([
            'title' => 'Tracked Tool',
            'url' => 'https://example.com/tracked',
            'slug' => 'tracked-tool',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'conversions' => 4,
            'is_active' => true,
        ]);

        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 0.25,
            'converted_at' => now()->subDays(2),
        ]);
        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 0.25,
            'converted_at' => now()->subDays(10),
        ]);
        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 0.25,
            'converted_at' => now()->subDays(40),
        ]);

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index'))
            ->assertOk()
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
