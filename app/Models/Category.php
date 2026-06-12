<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, Translatable, SoftDeletes;

    protected $fillable = ['parent_id', 'slug', 'description', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Translatable fields
    protected $translatable = ['name'];

    /**
     * Get parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get posts in this category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->where('status', 'published');
    }

    /**
     * Get SEO metadata.
     */
    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Get all ancestors (parent, grandparent, etc).
     */
    public function getAncestorsAttribute(): array
    {
        $ancestors = [];
        $parent = $this->parent;

        while ($parent) {
            $ancestors[] = $parent;
            $parent = $parent->parent;
        }

        return array_reverse($ancestors);
    }

    /**
     * Get breadcrumb path.
     */
    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [];

        foreach ($this->ancestors as $ancestor) {
            $breadcrumb[] = [
                'id' => $ancestor->id,
                'name' => $ancestor->name,
                'slug' => $ancestor->slug,
            ];
        }

        $breadcrumb[] = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];

        return $breadcrumb;
    }

    /**
     * Scope: Get active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get root categories (no parent).
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get categories by slug.
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get route key for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
