@php
    $selectedTagIds = collect(old('tag_ids', $post?->tags->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<div class="col-12">
    <label class="form-label" for="tag_ids">Tag</label>
    <select class="form-select @error('tag_ids') is-invalid @enderror @error('tag_ids.*') is-invalid @enderror" id="tag_ids" name="tag_ids[]" multiple size="5">
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTagIds, true))>
                {{ $tag->getTranslation('name', app()->getLocale()) ?? $tag->slug }}
            </option>
        @endforeach
    </select>
    @error('tag_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @error('tag_ids.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
