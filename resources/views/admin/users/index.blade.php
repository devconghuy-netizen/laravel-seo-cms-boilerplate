@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Quản lý user</h1>
            <p class="text-muted mb-0">Cấp role và bật/tắt tài khoản người dùng.</p>
            <div class="d-flex gap-3 mt-1">
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.categories.index') }}">Danh mục</a>
                <a href="{{ route('admin.tags.index') }}">Tag</a>
                <a href="{{ route('admin.media.index') }}">Media</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
                <a href="{{ route('admin.audit-logs.index') }}">Audit logs</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card card-body"><strong>{{ number_format($stats['total']) }}</strong><span class="text-muted">Tổng user</span></div></div>
        <div class="col-md-3"><div class="card card-body"><strong>{{ number_format($stats['active']) }}</strong><span class="text-muted">Đang hoạt động</span></div></div>
        <div class="col-md-3"><div class="card card-body"><strong>{{ number_format($stats['inactive']) }}</strong><span class="text-muted">Đang tắt</span></div></div>
        <div class="col-md-3"><div class="card card-body"><strong>{{ number_format($stats['admins']) }}</strong><span class="text-muted">Admin</span></div></div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-5">
                <label class="form-label" for="q">Tìm kiếm</label>
                <input class="form-control" id="q" name="q" value="{{ $filters['q'] }}" placeholder="Tên hoặc email">
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label" for="role">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Tất cả</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected($filters['role'] === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-lg-2">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tất cả</option>
                    <option value="active" @selected($filters['status'] === 'active')>Đang hoạt động</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Đang tắt</option>
                </select>
            </div>
            <div class="col-md-4 col-lg-2 text-end">
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Xóa lọc</a>
                <button class="btn btn-primary" type="submit">Lọc</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td>
                                <form id="user-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}">
                                    @csrf
                                    @method('PUT')
                                    <select class="form-select" name="role_ids[]" multiple size="{{ min(max($roles->count(), 3), 5) }}">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected($user->roles->contains($role))>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($user->is_active) @disabled(auth()->id() === $user->id)>
                                        <label class="form-check-label">{{ $user->is_active ? 'Đang hoạt động' : 'Đang tắt' }}</label>
                                    </div>
                                    @if(auth()->id() === $user->id)
                                        <input type="hidden" name="is_active" value="1">
                                    @endif
                                </form>
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" type="submit" form="user-form-{{ $user->id }}">Lưu</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="5">Không tìm thấy user phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
