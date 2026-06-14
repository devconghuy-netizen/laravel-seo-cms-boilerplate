@extends('layouts.app')

@section('title', '500 - Lỗi hệ thống')

@section('content')
    <div class="py-5 text-center">
        <div class="mx-auto" style="max-width: 640px;">
            <div class="display-4 fw-semibold mb-3">500</div>
            <h1 class="h3 mb-3">Hệ thống đang gặp sự cố</h1>
            <p class="text-muted mb-4">
                Có lỗi bất ngờ xảy ra trong lúc xử lý yêu cầu. Bạn có thể thử lại sau ít phút hoặc quay về trang chủ.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a class="btn btn-primary" href="{{ route('home') }}">Về trang chủ</a>
                @auth
                    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Về dashboard</a>
                @endauth
            </div>
        </div>
    </div>
@endsection
