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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ $siteSettings['site_name'] ?? 'AffiPress' }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Bài viết</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Sản phẩm</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('creator.posts.create') }}">Đăng bài</a></li>
                    @can('manageAll', App\Models\Post::class)
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.posts.index') }}">Quản trị</a></li>
                    @endcan
                    @if(auth()->user()->hasPermission('manage-users'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.audit-logs.index') }}">Audit</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.health.index') }}">Health</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings.edit') }}">Settings</a></li>
                    @endif
                @endauth
            </ul>
            <div class="d-flex gap-2 align-items-center">
                @guest
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Đăng ký viết bài</a>
                @else
                    @if(($navbarNotifications['visible'] ?? false))
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Notifications
                                @if(($navbarNotifications['unread_total'] ?? 0) > 0)
                                    <span class="badge text-bg-danger">{{ $navbarNotifications['unread_total'] }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 260px;">
                                <div class="dropdown-item d-flex justify-content-between align-items-start gap-3">
                                    <a class="text-decoration-none flex-grow-1" href="{{ route('admin.posts.index', ['status' => 'draft']) }}">
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
                                <div class="dropdown-item d-flex justify-content-between align-items-start gap-3">
                                    <a class="text-decoration-none flex-grow-1" href="{{ route('admin.affiliate-links.index') }}">
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
                                    <div class="dropdown-item d-flex justify-content-between align-items-start gap-3">
                                        <a class="text-decoration-none flex-grow-1" href="{{ route('admin.audit-logs.index') }}">
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
                                <div class="dropdown-divider my-0"></div>
                                <form method="POST" action="{{ route('notifications.read-all') }}" class="px-3 py-2">
                                    @csrf
                                    <button class="btn btn-outline-secondary btn-sm w-100" type="submit">Đánh dấu tất cả đã đọc</button>
                                </form>
                                <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">Notification center</a>
                            </div>
                        </div>
                    @endif
                    <a class="text-muted small text-decoration-none" href="{{ route('profile.show') }}">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Đăng xuất</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>
<div class="container mt-4">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @yield('content')
</div>
@if(! empty($siteSettings['facebook_url']) || ! empty($siteSettings['youtube_url']) || ! empty($siteSettings['tiktok_url']))
    <footer class="border-top mt-5 py-4">
        <div class="container d-flex gap-3 small">
            @if(! empty($siteSettings['facebook_url']))
                <a href="{{ $siteSettings['facebook_url'] }}" rel="noopener">Facebook</a>
            @endif
            @if(! empty($siteSettings['youtube_url']))
                <a href="{{ $siteSettings['youtube_url'] }}" rel="noopener">YouTube</a>
            @endif
            @if(! empty($siteSettings['tiktok_url']))
                <a href="{{ $siteSettings['tiktok_url'] }}" rel="noopener">TikTok</a>
            @endif
        </div>
    </footer>
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
