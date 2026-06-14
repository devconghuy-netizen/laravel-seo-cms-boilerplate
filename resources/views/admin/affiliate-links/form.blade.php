<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Tiêu đề</label>
        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $affiliateLink?->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="type">Loại</label>
        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
            @foreach(['product' => 'Product', 'service' => 'Service', 'offer' => 'Offer'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $affiliateLink?->type ?? 'product') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="form-label" for="url">URL affiliate</label>
        <input class="form-control @error('url') is-invalid @enderror" id="url" name="url" type="url" value="{{ old('url', $affiliateLink?->url) }}" required>
        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="slug">Slug</label>
        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $affiliateLink?->slug) }}" placeholder="Tự tạo nếu để trống">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="affiliate_program">Affiliate program</label>
        <input class="form-control @error('affiliate_program') is-invalid @enderror" id="affiliate_program" name="affiliate_program" value="{{ old('affiliate_program', $affiliateLink?->affiliate_program) }}" required>
        @error('affiliate_program')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="product_id">Product ID</label>
        <input class="form-control @error('product_id') is-invalid @enderror" id="product_id" name="product_id" value="{{ old('product_id', $affiliateLink?->product_id) }}">
        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="commission_rate">Commission rate (%)</label>
        <input class="form-control @error('commission_rate') is-invalid @enderror" id="commission_rate" name="commission_rate" type="number" min="0" max="100" step="0.01" value="{{ old('commission_rate', $affiliateLink?->commission_rate) }}">
        @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label class="form-label" for="post_id">Gắn với bài viết</label>
        <select class="form-select @error('post_id') is-invalid @enderror" id="post_id" name="post_id">
            <option value="">Không gắn bài viết</option>
            @foreach($posts as $post)
                <option value="{{ $post->id }}" @selected(old('post_id', $affiliateLink?->post_id) == $post->id)>
                    {{ $post->getTranslation('title', app()->getLocale()) ?? $post->slug }}
                </option>
            @endforeach
        </select>
        @error('post_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $affiliateLink?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Đang bật</label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Mô tả</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $affiliateLink?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu affiliate link</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.affiliate-links.index') }}">Hủy</a>
</div>
