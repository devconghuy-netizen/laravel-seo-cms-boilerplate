<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthorPostController extends Controller
{
    public function index()
    {
        $posts = Post::where('author_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('creator.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('sort_order')->orderBy('slug')->get();

        return view('creator.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'featured_image' => ['nullable', 'url', 'max:2048'],
        ]);

        $post = Post::create([
            'category_id' => $data['category_id'],
            'author_id' => Auth::id(),
            'slug' => $this->uniqueSlug($data['title']),
            'featured_image' => $data['featured_image'] ?? null,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        $post->setTranslation('title', $data['title'], app()->getLocale());
        $post->setTranslation('excerpt', $data['excerpt'] ?? '', app()->getLocale());
        $post->setTranslation('content', $data['content'], app()->getLocale());

        return redirect()
            ->route('creator.posts.index')
            ->with('status', 'Đã lưu bài viết.');
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
