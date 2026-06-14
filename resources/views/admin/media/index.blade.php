@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Thư viện media</h1>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.categories.index') }}">Danh mục</a>
                <a href="{{ route('admin.tags.index') }}">Tag</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($mediaItems as $media)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img class="card-img-top object-fit-cover" src="{{ '/storage/'.$media->path }}" alt="{{ $media->alt_text ?? $media->name }}" style="height: 160px;">
                    <div class="card-body">
                        <h2 class="h6 text-truncate" title="{{ $media->original_filename }}">{{ $media->original_filename }}</h2>
                        <p class="text-muted small mb-2">{{ $media->human_size }} · {{ $media->width }}x{{ $media->height }}</p>
                        <input class="form-control form-control-sm" value="{{ '/storage/'.$media->path }}" readonly>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <span class="text-muted small">{{ $media->user->name }}</span>
                        <form method="POST" action="{{ route('admin.media.destroy', $media) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa media này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Chưa có media nào.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $mediaItems->links() }}</div>
@endsection
