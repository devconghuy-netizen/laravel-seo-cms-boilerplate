@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Site settings</h1>
            <p class="text-muted mb-0">Cấu hình tên website, SEO mặc định và liên kết mạng xã hội.</p>
            <div class="d-flex gap-3 mt-1">
                <a href="{{ route('admin.users.index') }}">Users</a>
                <a href="{{ route('admin.audit-logs.index') }}">Audit logs</a>
                <a href="{{ route('admin.health.index') }}">Health</a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="card card-body">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="site_name">Tên website</label>
                <input class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required>
                @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="site_tagline">Tagline</label>
                <input class="form-control @error('site_tagline') is-invalid @enderror" id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}">
                @error('site_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="default_meta_description">Meta description mặc định</label>
                <textarea class="form-control @error('default_meta_description') is-invalid @enderror" id="default_meta_description" name="default_meta_description" rows="3">{{ old('default_meta_description', $settings['default_meta_description']) }}</textarea>
                @error('default_meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="default_og_image">Ảnh OG mặc định</label>
                <input class="form-control @error('default_og_image') is-invalid @enderror" id="default_og_image" name="default_og_image" value="{{ old('default_og_image', $settings['default_og_image']) }}" placeholder="https://example.com/cover.jpg">
                @error('default_og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="facebook_url">Facebook</label>
                <input class="form-control @error('facebook_url') is-invalid @enderror" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}">
                @error('facebook_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="youtube_url">YouTube</label>
                <input class="form-control @error('youtube_url') is-invalid @enderror" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url']) }}">
                @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="tiktok_url">TikTok</label>
                <input class="form-control @error('tiktok_url') is-invalid @enderror" id="tiktok_url" name="tiktok_url" value="{{ old('tiktok_url', $settings['tiktok_url']) }}">
                @error('tiktok_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary" type="submit">Lưu cấu hình</button>
        </div>
    </form>
@endsection
