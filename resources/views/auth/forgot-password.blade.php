@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h1 class="mb-2">Quên mật khẩu</h1>
            <p class="text-muted">Nhập email tài khoản, hệ thống sẽ gửi link đặt lại mật khẩu.</p>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="card card-body">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary" type="submit">Gửi link đặt lại mật khẩu</button>
            </form>

            <p class="mt-3"><a href="{{ route('login') }}">Quay lại đăng nhập</a></p>
        </div>
    </div>
@endsection
