@extends('layouts.app')

@section('title', '404 - Không tìm thấy')

@section('content')
    <div class="py-5 text-center">
        <div class="mx-auto" style="max-width: 640px;">
            <div class="display-4 fw-semibold mb-3">404</div>
            <h1 class="h3 mb-3">Không tìm thấy nội dung</h1>
            <p class="text-muted mb-4">
                Trang hoặc nội dung bạn đang tìm có thể đã bị xóa, đổi đường dẫn hoặc chưa được xuất bản.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a class="btn btn-primary" href="{{ route('home') }}">Xem bài viết</a>
                <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Xem sản phẩm</a>
            </div>
        </div>
    </div>
@endsection
