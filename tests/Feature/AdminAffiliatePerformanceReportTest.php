<?php

namespace Tests\Feature;

use App\Models\AffiliateClick;
use App\Models\AffiliateConversion;
use App\Models\AffiliateLink;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAffiliatePerformanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_view_program_and_link_performance_report(): void
    {
        $editor = $this->userWithRole('editor');
        $primary = $this->affiliateLink('Primary Tool', 'primary-tool', 'Partner A');
        $secondary = $this->affiliateLink('Secondary Tool', 'secondary-tool', 'Partner A');
        $other = $this->affiliateLink('Other Tool', 'other-tool', 'Partner B');

        $this->clicks($primary, 3, now()->subDays(2));
        $this->clicks($secondary, 2, now()->subDays(4));
        $this->clicks($other, 1, now()->subDays(3));
        $this->clicks($other, 10, now()->subDays(40));

        $this->conversions($primary, 2, 0.25, now()->subDays(2));
        $this->conversions($secondary, 1, 0.50, now()->subDays(4));
        $this->conversions($other, 1, 1.00, now()->subDays(40));

        $this->actingAs($editor)
            ->get(route('admin.affiliate-links.index'))
            ->assertOk()
            ->assertSee('Hiệu suất program 30 ngày')
            ->assertSee('Top link 30 ngày')
            ->assertViewHas('programReport', function ($report) {
                $partnerA = $report->firstWhere('program', 'Partner A');
                $partnerB = $report->firstWhere('program', 'Partner B');

                return $partnerA
                    && $partnerA['clicks'] === 5
                    && $partnerA['conversions'] === 3
                    && $partnerA['earnings'] === 1.0
                    && $partnerA['conversion_rate'] === 60.0
                    && $partnerB
                    && $partnerB['clicks'] === 1
                    && $partnerB['conversions'] === 0;
            })
            ->assertViewHas('linkReport', function ($report) use ($primary) {
                return $report->first()->id === $primary->id
                    && $report->first()->report_clicks === 3
                    && $report->first()->report_conversions === 2;
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

    private function affiliateLink(string $title, string $slug, string $program): AffiliateLink
    {
        return AffiliateLink::create([
            'title' => $title,
            'url' => "https://example.com/{$slug}",
            'slug' => $slug,
            'affiliate_program' => $program,
            'type' => 'product',
            'is_active' => true,
        ]);
    }

    private function clicks(AffiliateLink $link, int $count, $clickedAt): void
    {
        for ($i = 0; $i < $count; $i++) {
            AffiliateClick::create([
                'affiliate_link_id' => $link->id,
                'clicked_at' => $clickedAt,
            ]);
        }
    }

    private function conversions(AffiliateLink $link, int $count, float $amount, $convertedAt): void
    {
        for ($i = 0; $i < $count; $i++) {
            AffiliateConversion::create([
                'affiliate_link_id' => $link->id,
                'amount' => $amount,
                'converted_at' => $convertedAt,
            ]);
        }
    }
}
