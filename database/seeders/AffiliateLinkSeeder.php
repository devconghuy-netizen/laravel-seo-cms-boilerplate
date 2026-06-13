<?php

namespace Database\Seeders;

use App\Models\AffiliateLink;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AffiliateLinkSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $post = Post::published()->first();

        $products = [
            [
                'title' => 'Laravel Hosting Starter',
                'description' => 'Goi hosting phu hop de trien khai blog Laravel va website noi dung.',
                'slug' => 'laravel-hosting-starter',
                'affiliate_program' => 'AffiPress Demo',
                'product_id' => 'HOST-STARTER',
                'commission_rate' => 15,
                'type' => 'service',
                'url' => 'https://example.com/hosting',
            ],
            [
                'title' => 'SEO Toolkit Basic',
                'description' => 'Bo cong cu nghien cuu keyword, audit SEO va theo doi thu hang.',
                'slug' => 'seo-toolkit-basic',
                'affiliate_program' => 'AffiPress Demo',
                'product_id' => 'SEO-BASIC',
                'commission_rate' => 20,
                'type' => 'product',
                'url' => 'https://example.com/seo-toolkit',
            ],
            [
                'title' => 'Content Creator Camera Kit',
                'description' => 'Bo thiet bi quay video co ban cho nguoi tao noi dung va reviewer.',
                'slug' => 'content-creator-camera-kit',
                'affiliate_program' => 'AffiPress Demo',
                'product_id' => 'CAM-KIT',
                'commission_rate' => 8,
                'type' => 'product',
                'url' => 'https://example.com/camera-kit',
            ],
        ];

        foreach ($products as $product) {
            AffiliateLink::updateOrCreate(
                ['slug' => $product['slug']],
                array_merge($product, [
                    'post_id' => $post?->id,
                    'is_active' => true,
                ])
            );
        }
    }
}
