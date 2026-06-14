<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('manage', Category::class);

        $categories = Category::with('parent')
            ->orderBy('sort_order')
            ->orderBy('slug')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);

        $parents = Category::active()->orderBy('sort_order')->orderBy('slug')->get();

        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $data = $this->validatedCategoryData($request);
        $slug = $this->uniqueSlug($data['slug'] ?? $data['name']);

        $category = Category::create([
            'parent_id' => $data['parent_id'] ?? null,
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        $category->setTranslation('name', $data['name'], app()->getLocale());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã tạo danh mục.');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);

        $parents = Category::active()
            ->whereKeyNot($category->id)
            ->orderBy('sort_order')
            ->orderBy('slug')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $data = $this->validatedCategoryData($request, $category);
        $slug = $this->uniqueSlug($data['slug'] ?? $data['name'], $category);

        $category->update([
            'parent_id' => $data['parent_id'] ?? null,
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);
        $category->setTranslation('name', $data['name'], app()->getLocale());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        if ($category->children()->exists() || Post::where('category_id', $category->id)->exists()) {
            return back()->with('status', 'Không thể xóa danh mục đang có bài viết hoặc danh mục con.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Đã xóa danh mục.');
    }

    private function validatedCategoryData(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category?->id])],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $value, ?Category $category = null): string
    {
        $base = Str::slug($value) ?: 'category';
        $slug = $base;
        $counter = 2;

        while (Category::where('slug', $slug)->when($category, fn ($query) => $query->whereKeyNot($category->id))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
