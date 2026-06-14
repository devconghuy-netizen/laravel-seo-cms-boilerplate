<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminTagController extends Controller
{
    public function index()
    {
        $this->authorize('manage', Tag::class);

        $tags = Tag::orderBy('sort_order')
            ->orderBy('slug')
            ->paginate(15);

        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        $this->authorize('create', Tag::class);

        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Tag::class);

        $data = $this->validatedTagData($request);
        $slug = $this->uniqueSlug($data['slug'] ?? $data['name']);

        $tag = Tag::create([
            'slug' => $slug,
            'color' => $data['color'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        $tag->setTranslation('name', $data['name'], app()->getLocale());

        return redirect()
            ->route('admin.tags.index')
            ->with('status', 'Đã tạo tag.');
    }

    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);

        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $data = $this->validatedTagData($request, $tag);
        $slug = $this->uniqueSlug($data['slug'] ?? $data['name'], $tag);

        $tag->update([
            'slug' => $slug,
            'color' => $data['color'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        $tag->setTranslation('name', $data['name'], app()->getLocale());

        return redirect()
            ->route('admin.tags.index')
            ->with('status', 'Đã cập nhật tag.');
    }

    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag->posts()->detach();
        $tag->delete();

        return redirect()
            ->route('admin.tags.index')
            ->with('status', 'Đã xóa tag.');
    }

    private function validatedTagData(Request $request, ?Tag $tag = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tags', 'slug')->ignore($tag?->id),
            ],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $value, ?Tag $tag = null): string
    {
        $base = Str::slug($value) ?: 'tag';
        $slug = $base;
        $counter = 2;

        while (Tag::where('slug', $slug)->when($tag, fn ($query) => $query->whereKeyNot($tag->id))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
