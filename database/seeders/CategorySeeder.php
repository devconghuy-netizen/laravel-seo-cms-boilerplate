<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['slug' => 'technology', 'name' => 'Technology', 'description' => 'Tech news and tutorials'],
            ['slug' => 'business', 'name' => 'Business', 'description' => 'Business tips and insights'],
            ['slug' => 'lifestyle', 'name' => 'Lifestyle', 'description' => 'Lifestyle and wellness content'],
            ['slug' => 'marketing', 'name' => 'Marketing', 'description' => 'Digital marketing strategies'],
            ['slug' => 'seo', 'name' => 'SEO', 'description' => 'Search engine optimization'],
        ];

        foreach ($categories as $cat) {
            $category = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['is_active' => true]
            );

            // Add translations
            $category->setTranslation('name', $cat['name'], 'en');
            $category->setTranslation('description', $cat['description'], 'en');
            $category->save();
        }
    }
}
