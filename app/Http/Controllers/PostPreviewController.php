<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostPreviewController extends Controller
{
    public function __invoke(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->load(['author', 'category', 'tags', 'seoMeta']);

        return view('posts.show', [
            'post' => $post,
            'isPreview' => true,
        ]);
    }
}
