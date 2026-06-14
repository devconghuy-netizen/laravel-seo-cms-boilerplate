<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliateLinkFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_search_affiliate_links(): void
    {
        $editor = $this->userWithRole('editor');
        $matching = $this->affiliateLink('Laravel Tool', 'laravel-tool', 'Partner A', 'product');
        $other = $this->affiliateLink('SEO Tool', 'seo-tool', 'Partner B', 'service');

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index', ['q' => 'Laravel']))
            ->assertOk()
            ->assertViewHas('links', function ($links) use ($matching, $other) {
                $ids = $links->getCollection()->pluck('id');

                return $ids->contains($matching->id) && ! $ids->contains($other->id);
            });
    }

    public function test_editor_can_filter_affiliate_links_by_program_type_and_status(): void
    {
        $editor = $this->userWithRole('editor');
        $matching = $this->affiliateLink('Target Tool', 'target-tool', 'Partner A', 'product', true);
        $wrongProgram = $this->affiliateLink('Wrong Program', 'wrong-program', 'Partner B', 'product', true);
        $wrongType = $this->affiliateLink('Wrong Type', 'wrong-type', 'Partner A', 'service', true);
        $wrongStatus = $this->affiliateLink('Wrong Status', 'wrong-status', 'Partner A', 'product', false);

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index', [
                'program' => 'Partner A',
                'type' => 'product',
                'status' => 'active',
            ]))
            ->assertOk()
            ->assertViewHas('links', function ($links) use ($matching, $wrongProgram, $wrongType, $wrongStatus) {
                $ids = $links->getCollection()->pluck('id');

                return $ids->contains($matching->id)
                    && ! $ids->contains($wrongProgram->id)
                    && ! $ids->contains($wrongType->id)
                    && ! $ids->contains($wrongStatus->id);
            });
    }

    public function test_editor_can_sort_affiliate_links_by_earnings(): void
    {
        $editor = $this->userWithRole('editor');
        $low = $this->affiliateLink('Low Earner', 'low-earner', 'Partner A', 'product', true, 10, 1, 0.10);
        $high = $this->affiliateLink('High Earner', 'high-earner', 'Partner A', 'product', true, 5, 2, 5.25);

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index', ['sort' => 'earnings']))
            ->assertOk()
            ->assertViewHas('links', function ($links) use ($high, $low) {
                return $links->getCollection()->pluck('id')->take(2)->all() === [$high->id, $low->id];
            });
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

    private function affiliateLink(
        string $title,
        string $slug,
        string $program,
        string $type,
        bool $isActive = true,
        int $clicks = 0,
        int $conversions = 0,
        float $earnings = 0
    ): AffiliateLink {
        return AffiliateLink::create([
            'title' => $title,
            'url' => "https://example.com/{$slug}",
            'slug' => $slug,
            'affiliate_program' => $program,
            'type' => $type,
            'clicks' => $clicks,
            'conversions' => $conversions,
            'earnings' => $earnings,
            'is_active' => $isActive,
        ]);
    }
}
