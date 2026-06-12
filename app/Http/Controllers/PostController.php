<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;

class PostController
{
    public function __construct(private PostService $service)
    {
    }

    public function index(Request $request)
    {
        $posts = $this->service->listPublished(10);

        return view('posts.index', compact('posts'));
    }

    public function show(Request $request, $post)
    {
        $slug = is_string($post) ? $post : ($post->slug ?? null);

        $post = $this->service->findBySlug($slug);

        if (! $post) {
            abort(404);
        }

        return view('posts.show', compact('post'));
    }
}
