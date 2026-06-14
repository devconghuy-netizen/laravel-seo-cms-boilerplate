<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class TaxonomyController extends Controller
{
    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('posts.taxonomy', [
            'posts' => $posts,
            'title' => $category->getTranslation('name', app()->getLocale()) ?? $category->slug,
            'description' => $category->description,
            'type' => 'Danh mục',
        ]);
    }

    public function tag(Tag $tag)
    {
        abort_unless($tag->is_active, 404);

        $posts = Post::published()
            ->whereHas('tags', fn ($query) => $query->whereKey($tag->id))
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('posts.taxonomy', [
            'posts' => $posts,
            'title' => $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug,
            'description' => null,
            'type' => 'Tag',
        ]);
    }
}
