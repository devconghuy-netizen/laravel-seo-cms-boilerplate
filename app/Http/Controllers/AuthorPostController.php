<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthorPostController extends Controller
{
    public function __construct(private MediaUploadService $mediaUploadService)
    {
    }

    public function index()
    {
        $posts = Post::where('author_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('creator.posts.index', compact('posts'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);

        $categories = Category::active()->orderBy('sort_order')->orderBy('slug')->get();
        $tags = Tag::active()->orderBy('sort_order')->orderBy('slug')->get();

        return view('creator.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $data = $this->validatedPostData($request);
        $seoData = $this->validatedSeoData($request);
        $this->authorizeStatus($data['status']);
        $featuredImage = $this->resolveFeaturedImage($request, $data);

        $post = Post::create([
            'category_id' => $data['category_id'],
            'author_id' => Auth::id(),
            'slug' => $this->uniqueSlug($data['title']),
            'featured_image' => $featuredImage,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        $post->setTranslation('title', $data['title'], app()->getLocale());
        $post->setTranslation('excerpt', $data['excerpt'] ?? '', app()->getLocale());
        $post->setTranslation('content', $data['content'], app()->getLocale());
        $post->tags()->sync($data['tag_ids'] ?? []);
        $seoData = $this->resolveSeoImages($request, $seoData, $post);
        $this->syncSeoMeta($post, $seoData);

        return redirect()
            ->route('creator.posts.index')
            ->with('status', 'Đã lưu bài viết.');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::active()->orderBy('sort_order')->orderBy('slug')->get();
        $tags = Tag::active()->orderBy('sort_order')->orderBy('slug')->get();

        return view('creator.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $this->validatedPostData($request);
        $seoData = $this->validatedSeoData($request);
        $this->authorizeStatus($data['status'], $post);
        $wasPublished = $post->status === 'published';
        $featuredImage = $this->resolveFeaturedImage($request, $data, $post);

        $post->update([
            'category_id' => $data['category_id'],
            'featured_image' => $featuredImage,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published'
                ? ($wasPublished ? $post->published_at : now())
                : null,
        ]);

        $post->setTranslation('title', $data['title'], app()->getLocale());
        $post->setTranslation('excerpt', $data['excerpt'] ?? '', app()->getLocale());
        $post->setTranslation('content', $data['content'], app()->getLocale());
        $post->tags()->sync($data['tag_ids'] ?? []);
        $seoData = $this->resolveSeoImages($request, $seoData, $post);
        $this->syncSeoMeta($post, $seoData);

        return redirect()
            ->route('creator.posts.index')
            ->with('status', 'Đã cập nhật bài viết.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('creator.posts.index')
            ->with('status', 'Đã xóa bài viết.');
    }

    private function validatedPostData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'featured_image' => ['nullable', 'url', 'max:2048'],
            'featured_image_file' => ['nullable', 'image', 'max:4096'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);
    }

    private function authorizeStatus(string $status, ?Post $post = null): void
    {
        if ($status === 'published' && (! $post || $post->status !== 'published')) {
            $this->authorize('publish', $post ?? Post::class);
        }
    }

    private function validatedSeoData(Request $request): array
    {
        return $request->validate([
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'seo_canonical_url' => ['nullable', 'url', 'max:255'],
            'seo_og_title' => ['nullable', 'string', 'max:255'],
            'seo_og_description' => ['nullable', 'string', 'max:255'],
            'seo_og_image' => ['nullable', 'url', 'max:255'],
            'seo_og_image_file' => ['nullable', 'image', 'max:4096'],
            'seo_twitter_card' => ['nullable', 'in:summary,summary_large_image'],
            'seo_twitter_handle' => ['nullable', 'string', 'max:255'],
            'seo_index' => ['nullable', 'boolean'],
            'seo_follow' => ['nullable', 'boolean'],
        ]);
    }

    private function resolveFeaturedImage(Request $request, array $data, ?Post $post = null): ?string
    {
        if ($request->hasFile('featured_image_file')) {
            $media = $this->mediaUploadService->storeImage(
                $request->file('featured_image_file'),
                $request->user(),
                $data['title'] ?? null
            );

            return $this->mediaUploadService->publicUrl($media);
        }

        return $data['featured_image'] ?? $post?->featured_image;
    }

    private function resolveSeoImages(Request $request, array $data, Post $post): array
    {
        if ($request->hasFile('seo_og_image_file')) {
            $media = $this->mediaUploadService->storeImage(
                $request->file('seo_og_image_file'),
                $request->user(),
                $data['seo_og_title'] ?? $post->getTranslation('title', app()->getLocale())
            );

            $data['seo_og_image'] = $this->mediaUploadService->publicUrl($media);
        }

        return $data;
    }

    private function syncSeoMeta(Post $post, array $data): void
    {
        $post->seoMeta()->updateOrCreate(
            ['locale' => app()->getLocale()],
            [
                'title' => $data['seo_title'] ?? null,
                'description' => $data['seo_description'] ?? null,
                'keywords' => $data['seo_keywords'] ?? null,
                'canonical_url' => $data['seo_canonical_url'] ?? null,
                'og_title' => $data['seo_og_title'] ?? null,
                'og_description' => $data['seo_og_description'] ?? null,
                'og_image' => $data['seo_og_image'] ?? null,
                'og_type' => 'article',
                'twitter_card' => $data['seo_twitter_card'] ?? 'summary_large_image',
                'twitter_handle' => $data['seo_twitter_handle'] ?? null,
                'index' => (bool) ($data['seo_index'] ?? true),
                'follow' => (bool) ($data['seo_follow'] ?? true),
            ]
        );
    }

    private function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $base = $slug ?: 'post';
        $counter = 2;

        while (Post::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
