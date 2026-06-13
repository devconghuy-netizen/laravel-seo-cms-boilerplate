@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Bai viet cua toi</h1>
        <a class="btn btn-primary" href="{{ route('creator.posts.create') }}">Dang bai moi</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tieu de</th>
                        <th>Trang thai</th>
                        <th>Ngay tao</th>
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
                                @if($post->status === 'published')
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('posts.show', $post->slug) }}">Xem</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="4">Ban chua co bai viet nao.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $posts->links() }}</div>
@endsection
