@extends('layouts.app')

@section('title', $type . ': ' . $title . ' | ' . ($siteSettings['site_name'] ?? 'AffiPress'))

@section('meta')
    @if($description)
        <meta name="description" content="{{ Str::limit($description, 160) }}">
    @endif
@endsection

@section('content')
    <div class="mb-4">
        <p class="text-muted mb-1">{{ $type }}</p>
        <h1>{{ $title }}</h1>
        @if($description)
            <p class="text-muted">{{ $description }}</p>
        @endif
    </div>

    @forelse($posts as $post)
        @include('posts.partials.post-card', ['post' => $post])
    @empty
        <div class="alert alert-info">Chưa có bài viết nào.</div>
    @endforelse

    {{ $posts->links() }}
@endsection
