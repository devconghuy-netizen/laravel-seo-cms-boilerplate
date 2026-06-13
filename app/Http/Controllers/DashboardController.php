<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'my_posts' => Post::where('author_id', $user->id)->count(),
            'published_posts' => Post::where('author_id', $user->id)->where('status', 'published')->count(),
            'draft_posts' => Post::where('author_id', $user->id)->where('status', 'draft')->count(),
            'active_products' => AffiliateLink::active()->count(),
        ];

        $latestPosts = Post::where('author_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'latestPosts'));
    }
}
