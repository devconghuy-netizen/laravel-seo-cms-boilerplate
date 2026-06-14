@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Hồ sơ của tôi</h1>
            <p class="text-muted mb-0">Thông tin tài khoản, role và hoạt động gần đây.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Về dashboard</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">{{ $user->name }}</h2>
                    <div class="text-muted mb-2">{{ $user->email }}</div>
                    <div class="mb-3">
                        <span class="badge text-bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                            {{ $user->is_active ? 'Đang hoạt động' : 'Đang tắt' }}
                        </span>
                    </div>
                    <div class="small text-muted">Tham gia: {{ $user->created_at->format('Y-m-d') }}</div>
                    @if($user->last_login_at)
                        <div class="small text-muted">Đăng nhập gần nhất: {{ $user->last_login_at->format('Y-m-d H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">Role và quyền</div>
                <div class="card-body">
                    <div class="mb-3">
                        @forelse($user->roles as $role)
                            <span class="badge text-bg-primary me-1">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted">Chưa có role.</span>
                        @endforelse
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3"><div class="border rounded p-3"><strong>{{ number_format($stats['posts']) }}</strong><div class="text-muted small">Bài viết</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3"><strong>{{ number_format($stats['published_posts']) }}</strong><div class="text-muted small">Đã xuất bản</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3"><strong>{{ number_format($stats['draft_posts']) }}</strong><div class="text-muted small">Bản nháp</div></div></div>
                        <div class="col-md-3"><div class="border rounded p-3"><strong>{{ number_format($stats['audit_logs']) }}</strong><div class="text-muted small">Activity</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <form method="POST" action="{{ route('profile.update') }}" class="card card-body h-100">
                @csrf
                @method('PUT')
                <h2 class="h5 mb-3">Cập nhật thông tin</h2>
                <div class="mb-3">
                    <label class="form-label" for="name">Tên</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="phone_number">Số điện thoại</label>
                    <input class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Lưu thông tin</button>
                </div>
            </form>
        </div>

        <div class="col-lg-6">
            <form method="POST" action="{{ route('profile.password') }}" class="card card-body h-100">
                @csrf
                @method('PUT')
                <h2 class="h5 mb-3">Đổi mật khẩu</h2>
                <div class="mb-3">
                    <label class="form-label" for="current_password">Mật khẩu hiện tại</label>
                    <input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu mới</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Xác nhận mật khẩu mới</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <span class="fw-semibold">Bài viết gần đây</span>
                    <a href="{{ route('creator.posts.index') }}">Xem tất cả</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentPosts as $post)
                        <a class="list-group-item list-group-item-action d-flex justify-content-between gap-3" href="{{ route('creator.posts.edit', $post->slug) }}">
                            <div>
                                <div class="fw-semibold">{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</div>
                                <div class="text-muted small">{{ $post->category?->getTranslation('name', app()->getLocale()) ?? $post->category?->slug ?? 'Chưa có danh mục' }}</div>
                            </div>
                            <span class="badge text-bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ $post->status }}</span>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">Bạn chưa có bài viết nào.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white fw-semibold">Activity cá nhân</div>
                <div class="list-group list-group-flush">
                    @forelse($activityLogs as $log)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $log->action }}</div>
                                    <div class="text-muted small">{{ $log->description ?? class_basename($log->model_type).' #'.$log->model_id }}</div>
                                </div>
                                <div class="text-muted small text-end">{{ $log->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">Chưa có activity nào.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
