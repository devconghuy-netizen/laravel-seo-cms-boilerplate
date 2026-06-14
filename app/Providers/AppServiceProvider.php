<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\AffiliateConversion;
use App\Models\AuditLog;
use App\Models\NotificationRead;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Policies\CategoryPolicy;
use App\Policies\PostPolicy;
use App\Policies\TagPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);

        View::composer('*', function ($view) {
            $view->with('siteSettings', $this->siteSettings());
        });

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $notifications = [
                'visible' => false,
                'total' => 0,
                'unread_total' => 0,
                'draft_posts' => 0,
                'draft_posts_unread' => false,
                'conversions_7_days' => 0,
                'conversions_7_days_unread' => false,
                'audit_logs_24_hours' => 0,
                'audit_logs_24_hours_unread' => false,
            ];

            if ($user && $user->can('manageAll', Post::class)) {
                $notifications['visible'] = true;
                $notifications['draft_posts'] = Post::where('status', 'draft')->count();
                $notifications['conversions_7_days'] = AffiliateConversion::where('converted_at', '>=', now()->subDays(7))->count();
                $readAt = NotificationRead::where('user_id', $user->id)
                    ->pluck('read_at', 'notification_key');
                $latestDraft = Post::where('status', 'draft')->latest('created_at')->value('created_at');
                $latestConversion = AffiliateConversion::where('converted_at', '>=', now()->subDays(7))->latest('converted_at')->value('converted_at');
                $notifications['draft_posts_unread'] = $this->isUnread($latestDraft, $readAt->get('draft_posts'));
                $notifications['conversions_7_days_unread'] = $this->isUnread($latestConversion, $readAt->get('conversions_7_days'));

                if ($user->hasPermission('manage-users')) {
                    $notifications['audit_logs_24_hours'] = AuditLog::where('created_at', '>=', now()->subDay())->count();
                    $latestAudit = AuditLog::where('created_at', '>=', now()->subDay())->latest('created_at')->value('created_at');
                    $notifications['audit_logs_24_hours_unread'] = $this->isUnread($latestAudit, $readAt->get('audit_logs_24_hours'));
                }

                $notifications['total'] = $notifications['draft_posts']
                    + $notifications['conversions_7_days']
                    + $notifications['audit_logs_24_hours'];
                $notifications['unread_total'] = collect([
                    $notifications['draft_posts_unread'],
                    $notifications['conversions_7_days_unread'],
                    $notifications['audit_logs_24_hours_unread'],
                ])->filter()->count();
            }

            $view
                ->with('navbarNotifications', $notifications)
                ->with('siteSettings', $this->siteSettings());
        });
    }

    private function siteSettings(): array
    {
        $defaults = [
            'site_name' => 'AffiPress',
            'site_tagline' => '',
            'default_meta_description' => '',
            'default_og_image' => '',
            'facebook_url' => '',
            'youtube_url' => '',
            'tiktok_url' => '',
        ];

        if (! Schema::hasTable('site_settings')) {
            return $defaults;
        }

        return SiteSetting::values($defaults);
    }

    private function isUnread(mixed $latestAt, mixed $readAt): bool
    {
        if (! $latestAt) {
            return false;
        }

        if (! $readAt) {
            return true;
        }

        return Carbon::parse($latestAt)->gt(Carbon::parse($readAt));
    }
}
