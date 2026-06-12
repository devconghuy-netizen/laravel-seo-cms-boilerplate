@extends('layouts.app')

@section('content')
    <h1>Latest Posts</h1>

    @foreach($posts as $post)
        <article class="mb-4">
            <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</a></h2>
            <p class="text-muted">in <a href="#">{{ $post->category->getTranslation('name', app()->getLocale()) ?? $post->category->slug }}</a> — by {{ $post->author->name }}</p>
            <p>{{ Str::limit($post->getTranslation('content', app()->getLocale()) ?? '', 200) }}</p>
        </article>
    @endforeach

    {{ $posts->links() }}
@endsection
