@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-2">Xác thực email</h1>
            <p class="text-muted">
                Tài khoản của bạn cần xác thực email trước khi vào dashboard và khu vực quản trị nội dung.
                Hãy kiểm tra hộp thư để mở link xác thực.
            </p>

            @if(session('status') === 'verification-link-sent')
                <div class="alert alert-success">Đã gửi lại email xác thực.</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="card card-body">
                @csrf
                <div class="mb-3">
                    <div class="fw-semibold">{{ auth()->user()->email }}</div>
                    <div class="text-muted small">Email xác thực sẽ được gửi tới địa chỉ này.</div>
                </div>
                <button class="btn btn-primary" type="submit">Gửi lại email xác thực</button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="btn btn-link p-0" type="submit">Đăng xuất</button>
            </form>
        </div>
    </div>
@endsection
