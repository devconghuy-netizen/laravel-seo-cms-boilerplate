<article class="mb-4">
    <h2><a href="{{ route('posts.show', $post->slug) }}">{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</a></h2>
    <p class="text-muted">
        Trong <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->getTranslation('name', app()->getLocale()) ?? $post->category->slug }}</a>
        - bởi {{ $post->author->name }}
    </p>
    <p>{{ Str::limit($post->getTranslation('excerpt', app()->getLocale()) ?: $post->getTranslation('content', app()->getLocale()) ?? '', 200) }}</p>

    @if($post->tags->isNotEmpty())
        <p class="mb-0">
            @foreach($post->tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}" class="badge bg-secondary text-decoration-none">{{ $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug }}</a>
            @endforeach
        </p>
    @endif
</article>
