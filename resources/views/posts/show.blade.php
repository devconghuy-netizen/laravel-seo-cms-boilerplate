@extends('layouts.app')

@php
    $seo = $post->seoMeta;
    $postTitle = $post->getTranslation('title', app()->getLocale()) ?? $post->slug;
    $postExcerpt = $post->getTranslation('excerpt', app()->getLocale());
    $postContent = $post->getTranslation('content', app()->getLocale()) ?? '';
    $metaTitle = $seo?->title ?: $postTitle;
    $metaDescription = $seo?->description ?: Str::limit($postExcerpt ?: strip_tags($postContent), 160);
    $canonicalUrl = $seo?->canonical_url ?: route('posts.show', $post->slug);
    $ogTitle = $seo?->og_title ?: $metaTitle;
    $ogDescription = $seo?->og_description ?: $metaDescription;
    $ogImage = $seo?->og_image ?: $post->featured_image;
@endphp

@section('title', $metaTitle . ' | ' . ($siteSettings['site_name'] ?? 'AffiPress'))

@section('meta')
    <meta name="description" content="{{ $metaDescription }}">
    @if($seo?->keywords)
        <meta name="keywords" content="{{ $seo->keywords }}">
    @endif
    <meta name="robots" content="{{ $seo?->robots ?? 'index,follow' }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? 'AffiPress' }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:type" content="{{ $seo?->og_type ?? 'article' }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta name="twitter:card" content="{{ $seo?->twitter_card ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if($seo?->twitter_handle)
        <meta name="twitter:creator" content="{{ $seo->twitter_handle }}">
    @endif
@endsection

@section('content')
    @if($isPreview ?? false)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                <strong>Preview mode</strong>
                <span class="ms-2">Bài viết này chưa chắc đang public. Link preview chỉ hợp lệ tạm thời.</span>
            </div>
            <span class="badge text-bg-secondary">{{ $post->status }}</span>
        </div>
    @endif

    <article>
        <h1>{{ $postTitle }}</h1>
        <p class="text-muted">
            Trong <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->getTranslation('name', app()->getLocale()) ?? $post->category->slug }}</a>
            - bởi {{ $post->author->name }}
        </p>

        <div class="mb-4">
            {!! nl2br(e($postContent)) !!}
        </div>

        @if($post->tags->isNotEmpty())
            <p>Tags:
                @foreach($post->tags as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" class="badge bg-secondary text-decoration-none">{{ $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug }}</a>
                @endforeach
            </p>
        @endif
    </article>

    <p><a href="{{ route('home') }}">Quay lại danh sách bài viết</a></p>
@endsection
