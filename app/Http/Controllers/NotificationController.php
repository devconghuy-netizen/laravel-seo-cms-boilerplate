<?php

namespace App\Http\Controllers;

use App\Models\AffiliateConversion;
use App\Models\AuditLog;
use App\Models\NotificationRead;
use App\Models\Post;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private const ALLOWED_KEYS = [
        'draft_posts',
        'conversions_7_days',
        'audit_logs_24_hours',
    ];

    public function index(Request $request)
    {
        abort_unless($request->user()?->can('manageAll', Post::class), 403);

        $reads = NotificationRead::where('user_id', $request->user()->id)
            ->pluck('read_at', 'notification_key');

        $draftPosts = Post::with(['author', 'category'])
            ->where('status', 'draft')
            ->latest()
            ->limit(10)
            ->get();

        $recentConversions = AffiliateConversion::with('affiliateLink')
            ->where('converted_at', '>=', now()->subDays(7))
            ->latest('converted_at')
            ->limit(10)
            ->get();

        $recentAuditLogs = collect();

        if ($request->user()->hasPermission('manage-users')) {
            $recentAuditLogs = AuditLog::with('user')
                ->where('created_at', '>=', now()->subDay())
                ->latest()
                ->limit(10)
                ->get();
        }

        $readState = [
            'draft_posts' => $this->readStateFor($draftPosts->max('created_at'), $reads->get('draft_posts')),
            'conversions_7_days' => $this->readStateFor($recentConversions->max('converted_at'), $reads->get('conversions_7_days')),
            'audit_logs_24_hours' => $this->readStateFor($recentAuditLogs->max('created_at'), $reads->get('audit_logs_24_hours')),
        ];

        return view('notifications.index', compact('draftPosts', 'recentConversions', 'recentAuditLogs', 'readState'));
    }

    public function markRead(Request $request, string $key)
    {
        abort_unless(in_array($key, self::ALLOWED_KEYS, true), 404);

        NotificationRead::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'notification_key' => $key,
            ],
            ['read_at' => now()]
        );

        return back()->with('status', 'Đã đánh dấu notification là đã đọc.');
    }

    public function markAllRead(Request $request)
    {
        foreach (self::ALLOWED_KEYS as $key) {
            NotificationRead::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'notification_key' => $key,
                ],
                ['read_at' => now()]
            );
        }

        return back()->with('status', 'Đã đánh dấu tất cả notification là đã đọc.');
    }

    private function readStateFor(mixed $latestAt, mixed $readAt): array
    {
        return [
            'read_at' => $readAt,
            'unread' => $latestAt && (! $readAt || $latestAt->gt($readAt)),
        ];
    }
}
