<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Tên tag</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tag?->getTranslation('name', app()->getLocale())) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="slug">Slug</label>
        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $tag?->slug) }}" placeholder="Tự tạo nếu để trống">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="color">Màu</label>
        <input class="form-control @error('color') is-invalid @enderror" id="color" name="color" type="text" value="{{ old('color', $tag?->color) }}" placeholder="#0d6efd">
        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="sort_order">Thứ tự</label>
        <input class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $tag?->sort_order ?? 0) }}">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $tag?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Đang bật</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu tag</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.tags.index') }}">Hủy</a>
</div>
