@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Audit logs</h1>
            <p class="text-muted mb-0">Theo dõi các hành động quản trị quan trọng.</p>
            <div class="d-flex gap-3 mt-1">
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label" for="action">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">Tất cả</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="user_id">User</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">Tất cả</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) $filters['user_id'] === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 text-end">
                <a class="btn btn-outline-secondary" href="{{ route('admin.audit-logs.index') }}">Xóa lọc</a>
                <button class="btn btn-primary" type="submit">Lọc</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div>{{ $log->user?->name ?? 'System' }}</div>
                                <div class="text-muted small">{{ $log->ip_address }}</div>
                            </td>
                            <td><span class="badge text-bg-secondary">{{ $log->action }}</span></td>
                            <td>
                                <div>{{ class_basename($log->model_type) }} #{{ $log->model_id }}</div>
                                <div class="text-muted small">{{ $log->description }}</div>
                            </td>
                            <td class="small">
                                @if($log->old_values)
                                    <div><strong>Old:</strong> {{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }}</div>
                                @endif
                                @if($log->new_values)
                                    <div><strong>New:</strong> {{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="5">Chưa có audit log nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>
@endsection
