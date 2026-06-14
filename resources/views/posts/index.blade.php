@extends('layouts.app')

@section('title', ($search ? 'Tìm kiếm: ' . $search : 'Bài viết mới nhất') . ' | ' . ($siteSettings['site_name'] ?? 'AffiPress'))

@section('content')
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1>{{ $search ? 'Kết quả tìm kiếm' : 'Bài viết mới nhất' }}</h1>
            @if($search)
                <p class="text-muted mb-0">Từ khóa: <strong>{{ $search }}</strong></p>
            @endif
        </div>
        @if($search)
            <a class="btn btn-outline-secondary" href="{{ route('home') }}">Bỏ tìm kiếm</a>
        @endif
    </div>

    <form method="GET" action="{{ route('home') }}" class="card card-body mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-10">
                <label class="form-label" for="q">Tìm bài viết</label>
                <input class="form-control" id="q" name="q" value="{{ $search }}" placeholder="Nhập tiêu đề, nội dung hoặc slug">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Tìm</button>
            </div>
        </div>
    </form>

    @forelse($posts as $post)
        @include('posts.partials.post-card', ['post' => $post])
    @empty
        <div class="alert alert-info">Không tìm thấy bài viết phù hợp.</div>
    @endforelse

    {{ $posts->links() }}
@endsection
