@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Quản lý bài viết</h1>
            <p class="text-muted mb-0">Duyệt, xuất bản và quản trị toàn bộ bài viết.</p>
            <div class="d-flex gap-3 mt-1">
                <a href="{{ route('admin.categories.index') }}">Danh mục</a>
                <a href="{{ route('admin.tags.index') }}">Tag</a>
                <a href="{{ route('admin.media.index') }}">Media</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
            </div>
        </div>
        <a class="btn btn-primary" href="{{ route('creator.posts.create') }}">Tạo bài mới</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><a class="card card-body text-decoration-none" href="{{ route('admin.posts.index') }}"><strong>{{ $stats['all'] }}</strong><span class="text-muted">Tất cả</span></a></div>
        <div class="col-md-3"><a class="card card-body text-decoration-none" href="{{ route('admin.posts.index', ['status' => 'published']) }}"><strong>{{ $stats['published'] }}</strong><span class="text-muted">Đã xuất bản</span></a></div>
        <div class="col-md-3"><a class="card card-body text-decoration-none" href="{{ route('admin.posts.index', ['status' => 'draft']) }}"><strong>{{ $stats['draft'] }}</strong><span class="text-muted">Bản nháp</span></a></div>
        <div class="col-md-3"><a class="card card-body text-decoration-none" href="{{ route('admin.posts.index', ['status' => 'archived']) }}"><strong>{{ $stats['archived'] }}</strong><span class="text-muted">Đã lưu trữ</span></a></div>
    </div>

    <form method="GET" action="{{ route('admin.posts.index') }}" class="card card-body mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label" for="q">Tìm kiếm</label>
                <input class="form-control" id="q" name="q" value="{{ $filters['q'] }}" placeholder="Tiêu đề hoặc slug">
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tất cả</option>
                    <option value="draft" @selected($filters['status'] === 'draft')>Bản nháp</option>
                    <option value="published" @selected($filters['status'] === 'published')>Đã xuất bản</option>
                    <option value="archived" @selected($filters['status'] === 'archived')>Đã lưu trữ</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="category_id">Danh mục</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">Tất cả</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>
                            {{ $category->getTranslation('name', app()->getLocale()) ?? $category->slug }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="author_id">Tác giả</label>
                <select class="form-select" id="author_id" name="author_id">
                    <option value="">Tất cả</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" @selected((string) $filters['author_id'] === (string) $author->id)>{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label" for="sort">Sắp xếp</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="latest" @selected($filters['sort'] === 'latest')>Mới nhất</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Cũ nhất</option>
                    <option value="views" @selected($filters['sort'] === 'views')>Nhiều lượt xem</option>
                    <option value="published" @selected($filters['sort'] === 'published')>Ngày xuất bản</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">Tìm thấy {{ number_format($posts->total()) }} bài viết.</div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.posts.index') }}">Xóa lọc</a>
                <button class="btn btn-primary" type="submit">Áp dụng</button>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th>Lượt xem</th>
                        <th>Ngày tạo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</td>
                            <td>{{ $post->author->name }}</td>
                            <td>{{ $post->category->getTranslation('name', app()->getLocale()) ?? $post->category->slug }}</td>
                            <td><span class="badge text-bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ $post->status }}</span></td>
                            <td>{{ number_format($post->views_count) }}</td>
                            <td>{{ $post->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-dark" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('posts.preview', now()->addMinutes(30), ['post' => $post->slug]) }}" target="_blank">Preview</a>
                                @if($post->status === 'published')
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('posts.show', $post->slug) }}">Xem</a>
                                @endif
                                @can('publish', $post)
                                    @if($post->status !== 'published')
                                        <form method="POST" action="{{ route('admin.posts.publish', $post->slug) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Xuất bản</button>
                                        </form>
                                    @endif
                                @endcan
                                @can('update', $post)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.posts.edit', $post->slug) }}">Sửa</a>
                                @endcan
                                @can('delete', $post)
                                    <form method="POST" action="{{ route('admin.posts.destroy', $post->slug) }}" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="7">Không tìm thấy bài viết phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $posts->links() }}</div>
@endsection
