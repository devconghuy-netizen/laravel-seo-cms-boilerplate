@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Notification center</h1>
            <p class="text-muted mb-0">Xem các việc mới cần xử lý trong hệ thống.</p>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">Đánh dấu tất cả đã đọc</button>
        </form>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <span class="fw-semibold">Bản nháp cần duyệt</span>
                    @if($readState['draft_posts']['unread'])
                        <span class="badge text-bg-danger">Mới</span>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    @forelse($draftPosts as $post)
                        <div class="list-group-item d-flex justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold">{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</div>
                                <div class="text-muted small">{{ $post->author?->name ?? 'Không rõ tác giả' }} · {{ $post->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.posts.edit', $post->slug) }}">Sửa</a>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">Không có bài nháp nào cần duyệt.</div>
                    @endforelse
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}">Xem tất cả</a>
                    <form method="POST" action="{{ route('notifications.read', 'draft_posts') }}">
                        @csrf
                        <button class="btn btn-link btn-sm p-0" type="submit">Đánh dấu đã đọc</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <span class="fw-semibold">Conversion 7 ngày</span>
                    @if($readState['conversions_7_days']['unread'])
                        <span class="badge text-bg-danger">Mới</span>
                    @endif
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
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('admin.affiliate-links.index') }}">Xem affiliate</a>
                    <form method="POST" action="{{ route('notifications.read', 'conversions_7_days') }}">
                        @csrf
                        <button class="btn btn-link btn-sm p-0" type="submit">Đánh dấu đã đọc</button>
                    </form>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasPermission('manage-users'))
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <span class="fw-semibold">Audit 24h</span>
                        @if($readState['audit_logs_24_hours']['unread'])
                            <span class="badge text-bg-danger">Mới</span>
                        @endif
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentAuditLogs as $log)
                            <div class="list-group-item d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $log->action }}</div>
                                    <div class="text-muted small">{{ $log->description ?? class_basename($log->model_type).' #'.$log->model_id }}</div>
                                </div>
                                <div class="text-end small">
                                    <div>{{ $log->user?->name ?? 'System' }}</div>
                                    <div class="text-muted">{{ $log->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">Chưa có audit log trong 24h.</div>
                        @endforelse
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="{{ route('admin.audit-logs.index') }}">Xem audit logs</a>
                        <form method="POST" action="{{ route('notifications.read', 'audit_logs_24_hours') }}">
                            @csrf
                            <button class="btn btn-link btn-sm p-0" type="submit">Đánh dấu đã đọc</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
