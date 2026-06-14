<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_show_does_not_record_click(): void
    {
        $product = $this->affiliateLink();

        $this->get(route('products.show', $product->slug))->assertOk();

        $this->assertSame(0, $product->fresh()->clicks);
    }

    public function test_affiliate_redirect_records_click_and_redirects(): void
    {
        $product = $this->affiliateLink();

        $this->get(route('affiliate.redirect', $product->slug))
            ->assertRedirect('https://example.com/product');

        $this->assertSame(1, $product->fresh()->clicks);
        $this->assertDatabaseHas('affiliate_clicks', [
            'affiliate_link_id' => $product->id,
        ]);
    }

    public function test_inactive_affiliate_redirect_returns_404(): void
    {
        $product = $this->affiliateLink(['is_active' => false]);

        $this->get(route('affiliate.redirect', $product->slug))
            ->assertNotFound();
    }

    private function affiliateLink(array $overrides = []): AffiliateLink
    {
        return AffiliateLink::create($overrides + [
            'title' => 'Demo product',
            'description' => 'Demo product description',
            'url' => 'https://example.com/product',
            'slug' => 'demo-product',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);
    }
}
