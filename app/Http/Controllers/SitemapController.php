<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect([
            $this->url(route('home'), now(), 'daily', '1.0'),
            $this->url(route('products.index'), now(), 'daily', '0.8'),
        ]);

        Post::published()
            ->latest('published_at')
            ->get()
            ->each(fn (Post $post) => $urls->push($this->url(
                route('posts.show', $post),
                $post->updated_at ?? $post->published_at,
                'weekly',
                '0.9'
            )));

        AffiliateLink::active()
            ->latest()
            ->get()
            ->each(fn (AffiliateLink $product) => $urls->push($this->url(
                route('products.show', $product),
                $product->updated_at,
                'weekly',
                '0.8'
            )));

        Category::active()
            ->orderBy('sort_order')
            ->get()
            ->each(fn (Category $category) => $urls->push($this->url(
                route('categories.show', $category),
                $category->updated_at,
                'weekly',
                '0.7'
            )));

        Tag::active()
            ->orderBy('sort_order')
            ->get()
            ->each(fn (Tag $tag) => $urls->push($this->url(
                route('tags.show', $tag),
                $tag->updated_at,
                'weekly',
                '0.6'
            )));

        return response()
            ->view('sitemap.index', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /creator',
            'Disallow: /notifications',
            'Disallow: /profile',
            'Sitemap: '.route('sitemap.index'),
            '',
        ]);

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    private function url(string $loc, mixed $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => optional($lastmod)->toAtomString() ?? now()->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
