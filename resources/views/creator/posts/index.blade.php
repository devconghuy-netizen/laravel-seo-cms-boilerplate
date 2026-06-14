@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Bài viết của tôi</h1>
        <a class="btn btn-primary" href="{{ route('creator.posts.create') }}">Đăng bài mới</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>{{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}</td>
                            <td><span class="badge text-bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ $post->status }}</span></td>
                            <td>{{ $post->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-dark" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('posts.preview', now()->addMinutes(30), ['post' => $post->slug]) }}" target="_blank">Preview</a>
                                @if($post->status === 'published')
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('posts.show', $post->slug) }}">Xem</a>
                                @endif
                                @can('update', $post)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('creator.posts.edit', $post->slug) }}">Sửa</a>
                                @endcan
                                @can('delete', $post)
                                    <form method="POST" action="{{ route('creator.posts.destroy', $post->slug) }}" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài viết này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="4">Bạn chưa có bài viết nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $posts->links() }}</div>
@endsection
