<?php

namespace App\Http\Controllers;

use App\Models\AffiliateConversion;
use App\Models\AffiliateLink;
use App\Models\AuditLog;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $canManageAll = $user->can('manageAll', Post::class);
        $canManageUsers = $user->hasPermission('manage-users');

        $stats = [
            'my_posts' => Post::where('author_id', $user->id)->count(),
            'published_posts' => Post::where('author_id', $user->id)->where('status', 'published')->count(),
            'draft_posts' => Post::where('author_id', $user->id)->where('status', 'draft')->count(),
            'active_products' => AffiliateLink::active()->count(),
        ];

        $adminStats = [];
        $adminSignals = [];
        $activityTimeline = collect();
        $recentAuditLogs = collect();
        $recentConversions = collect();
        $reviewPosts = collect();
        $topAffiliateLinks = collect();

        if ($canManageAll) {
            $recentConversions = AffiliateConversion::with('affiliateLink')
                ->latest('converted_at')
                ->limit(5)
                ->get();

            $adminStats = [
                'total_posts' => Post::count(),
                'published_posts' => Post::where('status', 'published')->count(),
                'draft_posts' => Post::where('status', 'draft')->count(),
                'archived_posts' => Post::where('status', 'archived')->count(),
                'total_views' => Post::sum('views_count'),
                'active_affiliate_links' => AffiliateLink::active()->count(),
                'total_affiliate_clicks' => AffiliateLink::sum('clicks'),
                'total_affiliate_conversions' => AffiliateLink::sum('conversions'),
                'total_affiliate_earnings' => AffiliateLink::sum('earnings'),
            ];

            $adminSignals = [
                'draft_posts' => $adminStats['draft_posts'],
                'conversions_7_days' => AffiliateConversion::where('converted_at', '>=', now()->subDays(7))->count(),
                'imports_7_days' => AuditLog::where('action', 'affiliate.imported')->where('created_at', '>=', now()->subDays(7))->count(),
                'audit_logs_24_hours' => AuditLog::where('created_at', '>=', now()->subDay())->count(),
            ];

            if ($canManageUsers) {
                $recentAuditLogs = AuditLog::with('user')
                    ->latest()
                    ->limit(5)
                    ->get();

                $activityTimeline = AuditLog::with('user')
                    ->latest()
                    ->limit(15)
                    ->get()
                    ->groupBy(fn (AuditLog $log) => $log->created_at->format('Y-m-d'));
            }

            $reviewPosts = Post::with(['author', 'category'])
                ->where('status', 'draft')
                ->latest()
                ->limit(5)
                ->get();

            $topAffiliateLinks = AffiliateLink::with('post')
                ->orderByDesc('clicks')
                ->orderByDesc('conversions')
                ->limit(5)
                ->get();
        }

        $latestPosts = Post::with('category')
            ->where('author_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'adminStats',
            'adminSignals',
            'activityTimeline',
            'canManageAll',
            'canManageUsers',
            'latestPosts',
            'recentAuditLogs',
            'recentConversions',
            'reviewPosts',
            'topAffiliateLinks'
        ));
    }
}
