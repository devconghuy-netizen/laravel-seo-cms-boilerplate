@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">System health</h1>
            <p class="text-muted mb-0">Kiểm tra nhanh các thành phần vận hành chính của hệ thống.</p>
            <div class="d-flex gap-3 mt-1">
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.audit-logs.index') }}">Audit logs</a>
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
            </div>
        </div>
        <span class="badge fs-6 {{ $overallOk ? 'text-bg-success' : 'text-bg-danger' }}">
            {{ $overallOk ? 'Healthy' : 'Needs attention' }}
        </span>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checks as $check)
                        <tr>
                            <td class="fw-semibold">{{ $check['name'] }}</td>
                            <td>
                                <span class="badge {{ $check['status'] === 'ok' ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ strtoupper($check['status']) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $check['details'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
