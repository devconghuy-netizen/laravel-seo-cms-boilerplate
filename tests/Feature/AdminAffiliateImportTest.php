<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminAffiliateImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_import_affiliate_links_from_csv(): void
    {
        $editor = $this->userWithRole('editor');
        AffiliateLink::create([
            'title' => 'Old Tool',
            'url' => 'https://example.com/old',
            'slug' => 'old-tool',
            'affiliate_program' => 'Old Program',
            'type' => 'product',
            'is_active' => true,
        ]);

        $csv = implode("\n", [
            'title,url,affiliate_program,type,slug,description,product_id,commission_rate,is_active',
            'New Tool,https://example.com/new,Partner A,product,new-tool,New description,NEW-1,15.5,yes',
            'Updated Tool,https://example.com/updated,Partner B,service,old-tool,Updated description,OLD-1,20,no',
            'Broken Tool,not-a-url,Partner C,product,broken-tool,,,,yes',
        ]);

        $file = UploadedFile::fake()->createWithContent('affiliate-links.csv', $csv);

        $this->actingAs($editor)
            ->post(route('admin.affiliate-links.import'), ['csv_file' => $file])
            ->assertRedirect(route('admin.affiliate-links.index'));

        $this->assertDatabaseHas('affiliate_links', [
            'title' => 'New Tool',
            'slug' => 'new-tool',
            'affiliate_program' => 'Partner A',
            'product_id' => 'NEW-1',
            'commission_rate' => 15.5,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('affiliate_links', [
            'title' => 'Updated Tool',
            'slug' => 'old-tool',
            'affiliate_program' => 'Partner B',
            'type' => 'service',
            'is_active' => false,
        ]);

        $this->assertDatabaseMissing('affiliate_links', ['slug' => 'broken-tool']);
    }

    public function test_author_cannot_import_affiliate_links(): void
    {
        $author = $this->userWithRole('author');
        $file = UploadedFile::fake()->createWithContent('affiliate-links.csv', "title,url,affiliate_program,type\nTool,https://example.com,Demo,product");

        $this->actingAs($author)
            ->post(route('admin.affiliate-links.import'), ['csv_file' => $file])
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
