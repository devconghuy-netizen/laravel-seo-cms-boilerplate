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

class AdminAffiliateExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_export_affiliate_performance_csv(): void
    {
        $editor = $this->userWithRole('editor');
        $link = AffiliateLink::create([
            'title' => 'Export Tool',
            'url' => 'https://example.com/export-tool',
            'slug' => 'export-tool',
            'affiliate_program' => 'Export Partner',
            'type' => 'product',
            'is_active' => true,
        ]);

        AffiliateClick::create([
            'affiliate_link_id' => $link->id,
            'clicked_at' => now()->subDays(2),
        ]);
        AffiliateConversion::create([
            'affiliate_link_id' => $link->id,
            'amount' => 1.25,
            'converted_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($editor)
            ->get(route('admin.affiliate-links.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();

        $this->assertStringContainsString('section,program,link_title,link_slug,links,clicks,conversions,conversion_rate,earnings', $csv);
        $this->assertStringContainsString('program,"Export Partner",,,1,1,1,100,1.25', $csv);
        $this->assertStringContainsString('link,"Export Partner","Export Tool",export-tool,,1,1,100,1.25', $csv);
    }

    public function test_author_cannot_export_affiliate_performance_csv(): void
    {
        $author = $this->userWithRole('author');

        $this->actingAs($author)
            ->get(route('admin.affiliate-links.export'))
            ->assertForbidden();
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
