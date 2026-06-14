<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Tên danh mục</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category?->getTranslation('name', app()->getLocale())) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="slug">Slug</label>
        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category?->slug) }}" placeholder="Tự tạo nếu để trống">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="parent_id">Danh mục cha</label>
        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
            <option value="">Không có</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}" @selected(old('parent_id', $category?->parent_id) == $parent->id)>
                    {{ $parent->getTranslation('name', app()->getLocale()) ?? $parent->slug }}
                </option>
            @endforeach
        </select>
        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="sort_order">Thứ tự</label>
        <input class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category?->sort_order ?? 0) }}">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $category?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Đang bật</label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Mô tả</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu danh mục</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Hủy</a>
</div>
