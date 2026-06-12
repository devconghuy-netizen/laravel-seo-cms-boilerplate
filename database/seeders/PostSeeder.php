<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $posts = [
            [
                'slug' => 'laravel-tips',
                'title' => 'Laravel Tips & Tricks',
                'content' => 'Discover powerful Laravel tips to boost your development workflow. Learn about Eloquent optimization, caching strategies, and advanced routing techniques.',
                'category' => 'technology',
                'tags' => ['laravel', 'php', 'web-development'],
            ],
            [
                'slug' => 'seo-beginners-guide',
                'title' => 'SEO Beginner\'s Guide',
                'content' => 'Start your SEO journey with this comprehensive guide. Learn keyword research, on-page optimization, and link building strategies for better rankings.',
                'category' => 'seo',
                'tags' => ['seo-tips', 'content-marketing'],
            ],
            [
                'slug' => 'affiliate-marketing-101',
                'title' => 'Affiliate Marketing 101',
                'content' => 'Master the basics of affiliate marketing. Understand commission structures, finding the right programs, and promoting effectively to maximize earnings.',
                'category' => 'business',
                'tags' => ['affiliate-marketing', 'content-marketing'],
            ],
            [
                'slug' => 'cms-platform-comparison',
                'title' => 'CMS Platform Comparison',
                'content' => 'Compare popular CMS platforms including WordPress, Drupal, and modern headless options. Find the perfect fit for your project.',
                'category' => 'technology',
                'tags' => ['cms', 'wordpress', 'web-development'],
            ],
            [
                'slug' => 'content-marketing-strategy',
                'title' => 'Content Marketing Strategy Guide',
                'content' => 'Build a winning content marketing strategy. Learn audience research, content calendars, distribution channels, and measurement techniques.',
                'category' => 'marketing',
                'tags' => ['content-marketing', 'blogging'],
            ],
            [
                'slug' => 'automation-for-bloggers',
                'title' => 'Automation Tools for Bloggers',
                'content' => 'Discover automation tools that save time and increase productivity. Automate social sharing, email marketing, and content scheduling.',
                'category' => 'technology',
                'tags' => ['automation', 'blogging'],
            ],
            [
                'slug' => 'wordpress-plugins-must-have',
                'title' => 'Must-Have WordPress Plugins',
                'content' => 'Essential WordPress plugins for SEO, security, performance, and functionality. Create the perfect plugin stack for your site.',
                'category' => 'technology',
                'tags' => ['wordpress', 'cms'],
            ],
            [
                'slug' => 'business-blogging-benefits',
                'title' => 'Why Business Blogging Matters',
                'content' => 'Explore how business blogging drives traffic, builds authority, and generates leads. Strategic content creation for business growth.',
                'category' => 'business',
                'tags' => ['blogging', 'content-marketing'],
            ],
            [
                'slug' => 'php-best-practices',
                'title' => 'PHP Best Practices 2024',
                'content' => 'Learn modern PHP best practices including PSR standards, security patterns, testing approaches, and performance optimization.',
                'category' => 'technology',
                'tags' => ['php', 'web-development'],
            ],
            [
                'slug' => 'monetizing-blog-guide',
                'title' => 'Monetizing Your Blog: Complete Guide',
                'content' => 'Multiple strategies to monetize your blog: ads, affiliate links, sponsored content, digital products, and services.',
                'category' => 'business',
                'tags' => ['affiliate-marketing', 'blogging'],
            ],
        ];

        $admin = User::where('email', 'admin@affipress.test')->first();
        $editor = User::where('email', 'editor@affipress.test')->first();
        $authors = User::whereHas('roles', fn($q) => $q->where('name', 'author'))->get();

        foreach ($posts as $index => $postData) {
            $category = Category::where('slug', $postData['category'])->first();
            $user = $index % 2 == 0 ? $admin : ($authors->isNotEmpty() ? $authors->random() : $editor);

            $post = Post::firstOrCreate(
                ['slug' => $postData['slug']],
                [
                    'category_id' => $category?->id,
                    'author_id' => $user->id,
                    'status' => 'published',
                    'is_featured' => $index < 3,
                    'published_at' => now()->subDays(rand(0, 30)),
                ]
            );

            // Set translations
            $post->setTranslation('title', $postData['title'], 'en');
            $post->setTranslation('content', $postData['content'], 'en');
            $post->save();

            // Attach tags
            $tagIds = Tag::whereIn('slug', $postData['tags'])->pluck('id')->toArray();
            $post->tags()->sync($tagIds);
        }
    }
}
