@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.posts.index') }}">Quay lại quản lý bài viết</a>
        <h1 class="mt-2 mb-1">Chỉnh sửa bài viết</h1>
        <p class="text-muted mb-0">Tác giả: {{ $post->author->name }}</p>
    </div>

    <form method="POST" action="{{ route('admin.posts.update', $post->slug) }}" class="card card-body" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label" for="title">Tiêu đề</label>
                <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $post->getTranslation('title', app()->getLocale())) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="category_id">Danh mục</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>
                            {{ $category->getTranslation('name', app()->getLocale()) ?? $category->slug }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="excerpt">Tóm tắt</label>
                <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="2">{{ old('excerpt', $post->getTranslation('excerpt', app()->getLocale())) }}</textarea>
                @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="content">Nội dung bài viết</label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content', $post->getTranslation('content', app()->getLocale())) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="featured_image">Ảnh đại diện URL</label>
                <input class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" type="url" value="{{ old('featured_image', $post->featured_image) }}">
                @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="featured_image_file">Upload ảnh đại diện</label>
                <input class="form-control @error('featured_image_file') is-invalid @enderror" id="featured_image_file" name="featured_image_file" type="file" accept="image/*">
                @error('featured_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft" @selected(old('status', $post->status) === 'draft')>Bản nháp</option>
                    @can('publish', $post)
                        <option value="published" @selected(old('status', $post->status) === 'published')>Đã xuất bản</option>
                    @endcan
                    <option value="archived" @selected(old('status', $post->status) === 'archived')>Lưu trữ</option>
                </select>
            </div>

            @include('posts.partials.tag-fields', ['post' => $post])
        </div>

        @include('posts.partials.seo-fields', ['post' => $post])

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Cập nhật bài viết</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.posts.index') }}">Hủy</a>
        </div>
    </form>
@endsection
