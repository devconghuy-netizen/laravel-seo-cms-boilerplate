<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tags = [
            ['slug' => 'laravel', 'name' => 'Laravel'],
            ['slug' => 'php', 'name' => 'PHP'],
            ['slug' => 'web-development', 'name' => 'Web Development'],
            ['slug' => 'affiliate-marketing', 'name' => 'Affiliate Marketing'],
            ['slug' => 'seo-tips', 'name' => 'SEO Tips'],
            ['slug' => 'cms', 'name' => 'CMS'],
            ['slug' => 'wordpress', 'name' => 'WordPress'],
            ['slug' => 'blogging', 'name' => 'Blogging'],
            ['slug' => 'content-marketing', 'name' => 'Content Marketing'],
            ['slug' => 'automation', 'name' => 'Automation'],
        ];

        foreach ($tags as $t) {
            $tag = Tag::firstOrCreate(
                ['slug' => $t['slug']]
            );

            // Add translation
            $tag->setTranslation('name', $t['name'], 'en');
            $tag->save();
        }
    }
}
