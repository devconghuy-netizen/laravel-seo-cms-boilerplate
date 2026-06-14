@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Theo dõi nội dung, hiệu suất affiliate và các việc cần xử lý.</p>
        </div>
        <div class="d-flex gap-2">
            @if($canManageAll)
                <a class="btn btn-outline-secondary" href="{{ route('admin.posts.index') }}">Duyệt bài</a>
            @endif
            <a class="btn btn-primary" href="{{ route('creator.posts.create') }}">Đăng bài mới</a>
        </div>
    </div>

    @if($canManageAll)
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.posts.index') }}">
                    <strong class="fs-3">{{ number_format($adminStats['total_posts']) }}</strong>
                    <span class="text-muted">Tổng bài viết</span>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.posts.index', ['status' => 'draft']) }}">
                    <strong class="fs-3">{{ number_format($adminStats['draft_posts']) }}</strong>
                    <span class="text-muted">Bản nháp cần duyệt</span>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-body h-100">
                    <strong class="fs-3">{{ number_format($adminStats['total_views']) }}</strong>
                    <span class="text-muted">Lượt xem bài viết</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.affiliate-links.index') }}">
                    <strong class="fs-3">{{ number_format($adminStats['total_affiliate_clicks']) }}</strong>
                    <span class="text-muted">Affiliate clicks</span>
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.posts.index', ['status' => 'draft']) }}">
                    <span class="text-muted small">Cần duyệt</span>
                    <strong class="fs-4">{{ number_format($adminSignals['draft_posts']) }}</strong>
                </a>
            </div>
            <div class="col-md-3">
                <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.affiliate-links.index') }}">
                    <span class="text-muted small">Conversion 7 ngày</span>
                    <strong class="fs-4">{{ number_format($adminSignals['conversions_7_days']) }}</strong>
                </a>
            </div>
            <div class="col-md-3">
                <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.affiliate-links.index') }}">
                    <span class="text-muted small">Import affiliate 7 ngày</span>
                    <strong class="fs-4">{{ number_format($adminSignals['imports_7_days']) }}</strong>
                </a>
            </div>
            <div class="col-md-3">
                @if($canManageUsers)
                    <a class="card card-body h-100 text-decoration-none" href="{{ route('admin.audit-logs.index') }}">
                        <span class="text-muted small">Audit 24h</span>
                        <strong class="fs-4">{{ number_format($adminSignals['audit_logs_24_hours']) }}</strong>
                    </a>
                @else
                    <div class="card card-body h-100">
                        <span class="text-muted small">Audit 24h</span>
                        <strong class="fs-4">{{ number_format($adminSignals['audit_logs_24_hours']) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <span class="fw-semibold">Bài cần xử lý</span>
                        <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}">Xem tất cả</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($reviewPosts as $post)
                            <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</div>
                                    <div class="text-muted small">
                                        {{ $post->author?->name ?? 'Không rõ tác giả' }}
                                        @if($post->category)
                                            - {{ $post->category->getTranslation('name', app()->getLocale()) ?? $post->category->slug }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.posts.edit', $post->slug) }}">Sửa</a>
                                    @can('publish', $post)
                                        <form method="POST" action="{{ route('admin.posts.publish', $post->slug) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Xuất bản</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">Không có bài nháp nào cần xử lý.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <span class="fw-semibold">Affiliate performance</span>
                        <a href="{{ route('admin.affiliate-links.index') }}">Chi tiết</a>
                    </div>
                    <div class="card-body border-bottom">
                        <div class="row g-3 text-center">
                            <div class="col-4">
                                <div class="fw-semibold">{{ number_format($adminStats['active_affiliate_links']) }}</div>
                                <div class="text-muted small">Link bật</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-semibold">{{ number_format($adminStats['total_affiliate_conversions']) }}</div>
                                <div class="text-muted small">Conversion</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-semibold">${{ number_format($adminStats['total_affiliate_earnings'], 2) }}</div>
                                <div class="text-muted small">Earning</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($topAffiliateLinks as $link)
                            <div class="list-group-item d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $link->title }}</div>
                                    <div class="text-muted small">{{ $link->post?->slug ?? 'Không gắn bài viết' }}</div>
                                </div>
                                <div class="text-end small">
                                    <div>{{ number_format($link->clicks) }} click</div>
                                    <div>{{ number_format($link->conversions) }} conversion</div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">Chưa có dữ liệu affiliate.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <span class="fw-semibold">Conversion mới</span>
                        <a href="{{ route('admin.affiliate-links.index') }}">Xem affiliate</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentConversions as $conversion)
                            <div class="list-group-item d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $conversion->affiliateLink?->title ?? 'Affiliate link đã xóa' }}</div>
                                    <div class="text-muted small">{{ $conversion->converted_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="text-end">${{ number_format($conversion->amount, 2) }}</div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">Chưa có conversion mới.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($canManageUsers)
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <span class="fw-semibold">Audit log mới</span>
                            <a href="{{ route('admin.audit-logs.index') }}">Xem tất cả</a>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recentAuditLogs as $log)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $log->action }}</div>
                                            <div class="text-muted small">{{ $log->description ?? class_basename($log->model_type).' #'.$log->model_id }}</div>
                                        </div>
                                        <div class="text-end small">
                                            <div>{{ $log->user?->name ?? 'System' }}</div>
                                            <div class="text-muted">{{ $log->created_at->format('Y-m-d H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-muted">Chưa có audit log nào.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($canManageUsers)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <span class="fw-semibold">Activity timeline</span>
                    <a href="{{ route('admin.audit-logs.index') }}">Xem audit logs</a>
                </div>
                <div class="card-body">
                    @forelse($activityTimeline as $date => $logs)
                        <div class="mb-4">
                            <div class="text-muted small fw-semibold mb-2">{{ \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') }}</div>
                            <div class="border-start ps-3">
                                @foreach($logs as $log)
                                    <div class="mb-3 position-relative">
                                        <div class="fw-semibold">{{ $log->action }}</div>
                                        <div class="text-muted small">{{ $log->description ?? class_basename($log->model_type).' #'.$log->model_id }}</div>
                                        <div class="text-muted small">
                                            {{ $log->created_at->format('H:i') }}
                                            · {{ $log->user?->name ?? 'System' }}
                                            · {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Chưa có hoạt động quản trị nào.</div>
                    @endforelse
                </div>
            </div>
        @endif
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a class="card card-body text-decoration-none h-100" href="{{ route('creator.posts.index') }}">
                <strong class="fs-3">{{ number_format($stats['my_posts']) }}</strong>
                <span class="text-muted">Bài của tôi</span>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card card-body h-100">
                <strong class="fs-3">{{ number_format($stats['published_posts']) }}</strong>
                <span class="text-muted">Đã xuất bản</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body h-100">
                <strong class="fs-3">{{ number_format($stats['draft_posts']) }}</strong>
                <span class="text-muted">Bản nháp của tôi</span>
            </div>
        </div>
        <div class="col-md-3">
            <a class="card card-body text-decoration-none h-100" href="{{ route('products.index') }}">
                <strong class="fs-3">{{ number_format($stats['active_products']) }}</strong>
                <span class="text-muted">Sản phẩm public</span>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <span class="fw-semibold">Bài viết gần đây của tôi</span>
            <a href="{{ route('creator.posts.index') }}">Xem tất cả</a>
        </div>
        <div class="list-group list-group-flush">
            @forelse($latestPosts as $post)
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3" href="{{ route('creator.posts.edit', $post->slug) }}">
                    <div>
                        <div class="fw-semibold">{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</div>
                        <div class="text-muted small">
                            {{ $post->category?->getTranslation('name', app()->getLocale()) ?? $post->category?->slug ?? 'Chưa có danh mục' }}
                        </div>
                    </div>
                    <span class="badge text-bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ $post->status }}</span>
                </a>
            @empty
                <div class="list-group-item text-muted">Bạn chưa có bài viết nào.</div>
            @endforelse
        </div>
    </div>
@endsection
