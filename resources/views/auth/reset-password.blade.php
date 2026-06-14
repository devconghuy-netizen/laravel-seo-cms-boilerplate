@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h1 class="mb-2">Đặt lại mật khẩu</h1>
            <p class="text-muted">Tạo mật khẩu mới cho tài khoản của bạn.</p>

            <form method="POST" action="{{ route('password.update') }}" class="card card-body">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $email) }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu mới</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Nhập lại mật khẩu mới</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                </div>

                <button class="btn btn-primary" type="submit">Cập nhật mật khẩu</button>
            </form>
        </div>
    </div>
@endsection
