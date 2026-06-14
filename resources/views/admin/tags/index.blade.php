@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Quản lý tag</h1>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.categories.index') }}">Danh mục</a>
                <a href="{{ route('admin.media.index') }}">Media</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
            </div>
        </div>
        @can('create', App\Models\Tag::class)
            <a class="btn btn-primary" href="{{ route('admin.tags.create') }}">Tạo tag</a>
        @endcan
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Slug</th>
                        <th>Màu</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $tag)
                        <tr>
                            <td>{{ $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug }}</td>
                            <td>{{ $tag->slug }}</td>
                            <td>
                                @if($tag->color)
                                    <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->color }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $tag->sort_order }}</td>
                            <td><span class="badge text-bg-{{ $tag->is_active ? 'success' : 'secondary' }}">{{ $tag->is_active ? 'Đang bật' : 'Đang tắt' }}</span></td>
                            <td class="text-end">
                                @can('update', $tag)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tags.edit', $tag->slug) }}">Sửa</a>
                                @endcan
                                @can('delete', $tag)
                                    <form method="POST" action="{{ route('admin.tags.destroy', $tag->slug) }}" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa tag này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="6">Chưa có tag nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tags->links() }}</div>
@endsection
