@extends('layouts.app')

@section('content')
    <article>
        <h1>{{ $post->getTranslation('title', app()->getLocale()) }}</h1>
        <p class="text-muted">in <a href="#">{{ $post->category->getTranslation('name', app()->getLocale()) ?? $post->category->slug }}</a> - by {{ $post->author->name }}</p>

        <div class="mb-4">
            {!! nl2br(e($post->getTranslation('content', app()->getLocale()))) !!}
        </div>

        @if($post->tags->isNotEmpty())
            <p>Tags:
                @foreach($post->tags as $tag)
                    <a href="#" class="badge bg-secondary">{{ $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug }}</a>
                @endforeach
            </p>
        @endif
    </article>

    <p><a href="{{ route('home') }}">Back to posts</a></p>
@endsection
