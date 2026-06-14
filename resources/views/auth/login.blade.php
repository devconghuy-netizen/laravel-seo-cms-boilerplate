@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h1 class="mb-4">Đăng nhập</h1>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="card card-body">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>

                <button class="btn btn-primary" type="submit">Đăng nhập</button>
            </form>

            <div class="d-flex justify-content-between gap-3 mt-3">
                <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                <span>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký viết bài</a></span>
            </div>
        </div>
    </div>
@endsection
