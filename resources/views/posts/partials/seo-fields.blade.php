@php
    $seo = $post?->seoMeta;
@endphp

<hr class="my-4">
<h2 class="h5 mb-3">SEO</h2>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="seo_title">Meta title</label>
        <input class="form-control @error('seo_title') is-invalid @enderror" id="seo_title" name="seo_title" value="{{ old('seo_title', $seo?->title) }}" maxlength="255">
        @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="seo_keywords">Keywords</label>
        <input class="form-control @error('seo_keywords') is-invalid @enderror" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords', $seo?->keywords) }}">
        @error('seo_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="seo_description">Meta description</label>
        <textarea class="form-control @error('seo_description') is-invalid @enderror" id="seo_description" name="seo_description" rows="2" maxlength="160">{{ old('seo_description', $seo?->description) }}</textarea>
        @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="seo_canonical_url">Canonical URL</label>
        <input class="form-control @error('seo_canonical_url') is-invalid @enderror" id="seo_canonical_url" name="seo_canonical_url" type="url" value="{{ old('seo_canonical_url', $seo?->canonical_url) }}">
        @error('seo_canonical_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="seo_og_image">OG image URL</label>
        <input class="form-control @error('seo_og_image') is-invalid @enderror" id="seo_og_image" name="seo_og_image" type="url" value="{{ old('seo_og_image', $seo?->og_image) }}">
        @error('seo_og_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="seo_og_image_file">Upload OG image</label>
        <input class="form-control @error('seo_og_image_file') is-invalid @enderror" id="seo_og_image_file" name="seo_og_image_file" type="file" accept="image/*">
        @error('seo_og_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="seo_og_title">OG title</label>
        <input class="form-control @error('seo_og_title') is-invalid @enderror" id="seo_og_title" name="seo_og_title" value="{{ old('seo_og_title', $seo?->og_title) }}">
        @error('seo_og_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="seo_twitter_handle">Twitter handle</label>
        <input class="form-control @error('seo_twitter_handle') is-invalid @enderror" id="seo_twitter_handle" name="seo_twitter_handle" value="{{ old('seo_twitter_handle', $seo?->twitter_handle) }}" placeholder="@affipress">
        @error('seo_twitter_handle')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="seo_og_description">OG description</label>
        <textarea class="form-control @error('seo_og_description') is-invalid @enderror" id="seo_og_description" name="seo_og_description" rows="2">{{ old('seo_og_description', $seo?->og_description) }}</textarea>
        @error('seo_og_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="seo_twitter_card">Twitter card</label>
        <select class="form-select @error('seo_twitter_card') is-invalid @enderror" id="seo_twitter_card" name="seo_twitter_card">
            <option value="summary" @selected(old('seo_twitter_card', $seo?->twitter_card ?? 'summary_large_image') === 'summary')>summary</option>
            <option value="summary_large_image" @selected(old('seo_twitter_card', $seo?->twitter_card ?? 'summary_large_image') === 'summary_large_image')>summary_large_image</option>
        </select>
        @error('seo_twitter_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div>
            <input type="hidden" name="seo_index" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" id="seo_index" name="seo_index" type="checkbox" value="1" @checked(old('seo_index', $seo?->index ?? true))>
                <label class="form-check-label" for="seo_index">Cho index</label>
            </div>
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div>
            <input type="hidden" name="seo_follow" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" id="seo_follow" name="seo_follow" type="checkbox" value="1" @checked(old('seo_follow', $seo?->follow ?? true))>
                <label class="form-check-label" for="seo_follow">Cho follow</label>
            </div>
        </div>
    </div>
</div>
