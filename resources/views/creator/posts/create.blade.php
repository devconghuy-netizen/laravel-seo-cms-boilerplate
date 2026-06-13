@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Dang bai moi</h1>

    <form method="POST" action="{{ route('creator.posts.store') }}" class="card card-body">
        @csrf

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label" for="title">Tieu de</label>
                <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="category_id">Danh muc</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Chon danh muc</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->getTranslation('name', app()->getLocale()) ?? $category->slug }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="excerpt">Tom tat</label>
                <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="2">{{ old('excerpt') }}</textarea>
                @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="content">Noi dung bai viet</label>
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-8">
                <label class="form-label" for="featured_image">Anh dai dien URL</label>
                <input class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" type="url" value="{{ old('featured_image') }}">
                @error('featured_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="status">Trang thai</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft" @selected(old('status') === 'draft')>Luu ban nhap</option>
                    <option value="published" @selected(old('status') === 'published')>Xuat ban ngay</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Luu bai viet</button>
            <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Huy</a>
        </div>
    </form>
@endsection
