@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Quản lý danh mục</h1>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.posts.index') }}">Bài viết</a>
                <a href="{{ route('admin.tags.index') }}">Tag</a>
                <a href="{{ route('admin.media.index') }}">Media</a>
                <a href="{{ route('admin.affiliate-links.index') }}">Affiliate</a>
            </div>
        </div>
        @can('create', App\Models\Category::class)
            <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">Tạo danh mục</a>
        @endcan
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Slug</th>
                        <th>Danh mục cha</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->getTranslation('name', app()->getLocale()) ?? $category->slug }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->parent?->getTranslation('name', app()->getLocale()) ?? $category->parent?->slug ?? '-' }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td><span class="badge text-bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Đang bật' : 'Đang tắt' }}</span></td>
                            <td class="text-end">
                                @can('update', $category)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.categories.edit', $category->slug) }}">Sửa</a>
                                @endcan
                                @can('delete', $category)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category->slug) }}" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa danh mục này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-muted" colspan="6">Chưa có danh mục nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
@endsection
