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
    $wordCount = count(array_filter(preg_split('/\s+/u', trim(strip_tags($postContent)))));
    $readingMinutes = max(1, (int) ceil($wordCount / 220));
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
            @if($post->published_at)
                - {{ $post->published_at->format('d/m/Y') }}
            @endif
            - {{ $readingMinutes }} phút đọc
            - {{ number_format($post->views_count) }} lượt xem
        </p>

        @if($post->featured_image)
            <img class="img-fluid rounded mb-4" src="{{ $post->featured_image }}" alt="{{ $postTitle }}">
        @endif

        <div class="mb-4">
            {!! nl2br(e($postContent)) !!}
        </div>

        @if($post->affiliateLinks->isNotEmpty())
            <section class="card card-body mb-4">
                <h2 class="h4">Sản phẩm được nhắc tới</h2>
                <div class="list-group list-group-flush">
                    @foreach($post->affiliateLinks as $affiliateLink)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h3 class="h6 mb-1">{{ $affiliateLink->title }}</h3>
                                @if($affiliateLink->description)
                                    <p class="text-muted mb-1">{{ $affiliateLink->description }}</p>
                                @endif
                                <span class="badge text-bg-light">{{ $affiliateLink->affiliate_program }}</span>
                            </div>
                            <a class="btn btn-sm btn-primary" href="{{ route('affiliate.redirect', $affiliateLink->slug) }}" rel="nofollow sponsored" target="_blank">
                                Xem ưu đãi
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($post->tags->isNotEmpty())
            <p>Tags:
                @foreach($post->tags as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" class="badge bg-secondary text-decoration-none">{{ $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug }}</a>
                @endforeach
            </p>
        @endif
    </article>

    @if(($relatedPosts ?? collect())->isNotEmpty())
        <section class="mt-5">
            <h2 class="h4 mb-3">Bài viết liên quan</h2>
            @foreach($relatedPosts as $relatedPost)
                @include('posts.partials.post-card', ['post' => $relatedPost])
            @endforeach
        </section>
    @endif

    <p><a href="{{ route('home') }}">Quay lại danh sách bài viết</a></p>
@endsection
