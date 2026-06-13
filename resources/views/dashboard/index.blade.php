@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Quan ly noi dung cua ban tren AffiPress.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('creator.posts.create') }}">Dang bai moi</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card card-body"><strong>{{ $stats['my_posts'] }}</strong><span class="text-muted">Bai cua toi</span></div></div>
        <div class="col-md-3"><div class="card card-body"><strong>{{ $stats['published_posts'] }}</strong><span class="text-muted">Da xuat ban</span></div></div>
        <div class="col-md-3"><div class="card card-body"><strong>{{ $stats['draft_posts'] }}</strong><span class="text-muted">Ban nhap</span></div></div>
        <div class="col-md-3"><div class="card card-body"><strong>{{ $stats['active_products'] }}</strong><span class="text-muted">San pham public</span></div></div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Bai viet gan day</span>
            <a href="{{ route('creator.posts.index') }}">Xem tat ca</a>
        </div>
        <div class="list-group list-group-flush">
            @forelse($latestPosts as $post)
                <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('posts.show', $post->slug) }}">
                    <span>{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</span>
                    <span class="badge text-bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ $post->status }}</span>
                </a>
            @empty
                <div class="list-group-item text-muted">Ban chua co bai viet nao.</div>
            @endforelse
        </div>
    </div>
@endsection
