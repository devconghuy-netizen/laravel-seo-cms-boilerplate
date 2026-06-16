<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteSettings['site_name'] ?? 'AffiPress')</title>
    @hasSection('meta')
        @yield('meta')
    @else
        @if(! empty($siteSettings['default_meta_description']))
            <meta name="description" content="{{ $siteSettings['default_meta_description'] }}">
        @endif
        <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? 'AffiPress' }}">
        <meta property="og:title" content="{{ $siteSettings['site_name'] ?? 'AffiPress' }}">
        @if(! empty($siteSettings['default_meta_description']))
            <meta property="og:description" content="{{ $siteSettings['default_meta_description'] }}">
        @endif
        @if(! empty($siteSettings['default_og_image']))
            <meta property="og:image" content="{{ $siteSettings['default_og_image'] }}">
        @endif
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
<nav class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 flex-wrap items-center justify-between gap-3 py-3">
            <a class="text-lg font-semibold text-slate-950" href="{{ route('home') }}">{{ $siteSettings['site_name'] ?? 'AffiPress' }}</a>
            <button class="inline-flex items-center rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 lg:hidden" type="button" data-nav-toggle aria-controls="mainNav" aria-expanded="false">
                Menu
            </button>
            <div class="hidden w-full items-center justify-between gap-4 lg:flex lg:w-auto lg:flex-1" id="mainNav" data-nav-menu>
                <ul class="flex flex-col gap-1 lg:ml-6 lg:flex-row lg:items-center lg:gap-2">
                    <li><a class="nav-link" href="{{ route('home') }}">Bài viết</a></li>
                    <li><a class="nav-link" href="{{ route('products.index') }}">Sản phẩm</a></li>
                    @auth
                        <li><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a class="nav-link" href="{{ route('creator.posts.create') }}">Đăng bài</a></li>
                        @can('manageAll', App\Models\Post::class)
                            <li><a class="nav-link" href="{{ route('admin.posts.index') }}">Quản trị</a></li>
                        @endcan
                        @if(auth()->user()->hasPermission('manage-users'))
                            <li><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                            <li><a class="nav-link" href="{{ route('admin.audit-logs.index') }}">Audit</a></li>
                            <li><a class="nav-link" href="{{ route('admin.health.index') }}">Health</a></li>
                            <li><a class="nav-link" href="{{ route('admin.settings.edit') }}">Settings</a></li>
                        @endif
                    @endauth
                </ul>
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
                    @guest
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Đăng ký viết bài</a>
                    @else
                        @if(($navbarNotifications['visible'] ?? false))
                            <div class="relative" data-dropdown>
                                <button class="btn btn-outline-primary btn-sm" type="button" data-dropdown-toggle aria-expanded="false">
                                    Notifications
                                    @if(($navbarNotifications['unread_total'] ?? 0) > 0)
                                        <span class="badge text-bg-danger">{{ $navbarNotifications['unread_total'] }}</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu hidden w-72 lg:absolute lg:right-0 lg:z-20" data-dropdown-menu>
                                    <div class="dropdown-item flex justify-between gap-3">
                                        <a class="flex-grow text-slate-800 hover:text-blue-700" href="{{ route('admin.posts.index', ['status' => 'draft']) }}">
                                        <span>Bản nháp cần duyệt</span>
                                            @if($navbarNotifications['draft_posts_unread'])
                                                <span class="badge text-bg-danger ms-1">Mới</span>
                                            @endif
                                        </a>
                                        <div class="text-end">
                                            <strong>{{ number_format($navbarNotifications['draft_posts']) }}</strong>
                                            <form method="POST" action="{{ route('notifications.read', 'draft_posts') }}">
                                                @csrf
                                                <button class="btn btn-link btn-sm p-0" type="submit">Đã đọc</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="dropdown-item flex justify-between gap-3">
                                        <a class="flex-grow text-slate-800 hover:text-blue-700" href="{{ route('admin.affiliate-links.index') }}">
                                        <span>Conversion 7 ngày</span>
                                            @if($navbarNotifications['conversions_7_days_unread'])
                                                <span class="badge text-bg-danger ms-1">Mới</span>
                                            @endif
                                        </a>
                                        <div class="text-end">
                                            <strong>{{ number_format($navbarNotifications['conversions_7_days']) }}</strong>
                                            <form method="POST" action="{{ route('notifications.read', 'conversions_7_days') }}">
                                                @csrf
                                                <button class="btn btn-link btn-sm p-0" type="submit">Đã đọc</button>
                                            </form>
                                        </div>
                                    </div>
                                    @if(auth()->user()->hasPermission('manage-users'))
                                        <div class="dropdown-item flex justify-between gap-3">
                                            <a class="flex-grow text-slate-800 hover:text-blue-700" href="{{ route('admin.audit-logs.index') }}">
                                            <span>Audit 24h</span>
                                                @if($navbarNotifications['audit_logs_24_hours_unread'])
                                                    <span class="badge text-bg-danger ms-1">Mới</span>
                                                @endif
                                            </a>
                                            <div class="text-end">
                                                <strong>{{ number_format($navbarNotifications['audit_logs_24_hours']) }}</strong>
                                                <form method="POST" action="{{ route('notifications.read', 'audit_logs_24_hours') }}">
                                                    @csrf
                                                    <button class="btn btn-link btn-sm p-0" type="submit">Đã đọc</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="border-t border-slate-200"></div>
                                    <form method="POST" action="{{ route('notifications.read-all') }}" class="px-3 py-2">
                                        @csrf
                                        <button class="btn btn-outline-secondary btn-sm w-100" type="submit">Đánh dấu tất cả đã đọc</button>
                                    </form>
                                    <a class="dropdown-item block text-center" href="{{ route('notifications.index') }}">Notification center</a>
                                </div>
                            </div>
                        @endif
                        <a class="text-sm text-slate-500 hover:text-slate-900" href="{{ route('profile.show') }}">{{ auth()->user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-secondary btn-sm" type="submit">Đăng xuất</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @yield('content')
</main>
@if(! empty($siteSettings['facebook_url']) || ! empty($siteSettings['youtube_url']) || ! empty($siteSettings['tiktok_url']))
    <footer class="mt-10 border-t border-slate-200 bg-white py-6">
        <div class="mx-auto flex max-w-7xl gap-4 px-4 text-sm sm:px-6 lg:px-8">
            @if(! empty($siteSettings['facebook_url']))
                <a class="text-slate-600 hover:text-blue-700" href="{{ $siteSettings['facebook_url'] }}" rel="noopener">Facebook</a>
            @endif
            @if(! empty($siteSettings['youtube_url']))
                <a class="text-slate-600 hover:text-blue-700" href="{{ $siteSettings['youtube_url'] }}" rel="noopener">YouTube</a>
            @endif
            @if(! empty($siteSettings['tiktok_url']))
                <a class="text-slate-600 hover:text-blue-700" href="{{ $siteSettings['tiktok_url'] }}" rel="noopener">TikTok</a>
            @endif
        </div>
    </footer>
@endif
</body>
</html>
