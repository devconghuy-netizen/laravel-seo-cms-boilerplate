@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-2">Đăng ký người tạo nội dung</h1>
            <p class="text-muted">Tài khoản mới sẽ được gán vai trò author và có thể tạo bài viết.</p>

            <form method="POST" action="{{ route('register.store') }}" class="card card-body">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="name">Tên hiển thị</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                </div>

                <button class="btn btn-primary" type="submit">Tạo tài khoản</button>
            </form>
        </div>
    </div>
@endsection
