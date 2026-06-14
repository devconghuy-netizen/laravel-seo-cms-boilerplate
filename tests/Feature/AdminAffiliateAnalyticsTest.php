<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliateAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_affiliate_analytics(): void
    {
        $editor = $this->userWithRole('editor');

        AffiliateLink::create([
            'title' => 'Primary Tool',
            'url' => 'https://example.com/primary',
            'slug' => 'primary-tool',
            'affiliate_program' => 'Demo',
            'commission_rate' => 20,
            'type' => 'product',
            'clicks' => 10,
            'conversions' => 2,
            'earnings' => 0.40,
            'is_active' => true,
        ]);

        AffiliateLink::create([
            'title' => 'Backup Tool',
            'url' => 'https://example.com/backup',
            'slug' => 'backup-tool',
            'affiliate_program' => 'Demo',
            'commission_rate' => 10,
            'type' => 'service',
            'clicks' => 5,
            'conversions' => 1,
            'earnings' => 0.10,
            'is_active' => false,
        ]);

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index'))
            ->assertOk()
            ->assertSee('Top affiliate links')
            ->assertSee('15')
            ->assertSee('3')
            ->assertSee('20%')
            ->assertSee('$0.50');
    }

    public function test_editor_can_record_demo_conversion(): void
    {
        $editor = $this->userWithRole('editor');
        $link = AffiliateLink::create([
            'title' => 'Demo Tool',
            'url' => 'https://example.com/demo',
            'slug' => 'demo-tool',
            'affiliate_program' => 'Demo',
            'commission_rate' => 25,
            'type' => 'product',
            'clicks' => 4,
            'conversions' => 0,
            'earnings' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($editor)
            ->post(route('admin.affiliate-links.conversion', $link->slug))
            ->assertRedirect(route('admin.affiliate-links.index'));

        $link->refresh();

        $this->assertSame(1, $link->conversions);
        $this->assertSame(0.25, $link->earnings);
        $this->assertSame(25.0, $link->conversion_rate);
        $this->assertDatabaseHas('affiliate_conversions', [
            'affiliate_link_id' => $link->id,
            'amount' => 0.25,
        ]);
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
