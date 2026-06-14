@extends('layouts.app')

@section('title', '403 - Không có quyền')

@section('content')
    <div class="py-5 text-center">
        <div class="mx-auto" style="max-width: 640px;">
            <div class="display-4 fw-semibold mb-3">403</div>
            <h1 class="h3 mb-3">Bạn không có quyền truy cập trang này</h1>
            <p class="text-muted mb-4">
                Tài khoản hiện tại chưa được cấp quyền cho thao tác này. Nếu bạn nghĩ đây là nhầm lẫn, hãy liên hệ admin để kiểm tra role.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a class="btn btn-primary" href="{{ route('dashboard') }}">Về dashboard</a>
                <a class="btn btn-outline-secondary" href="{{ route('home') }}">Về trang chủ</a>
            </div>
        </div>
    </div>
@endsection
