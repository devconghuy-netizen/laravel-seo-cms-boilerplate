<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, Translatable;

    protected $fillable = ['slug', 'color', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Translatable fields
    protected $translatable = ['name'];

    /**
     * Get all posts with this tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')
            ->where('status', 'published')
            ->withTimestamps();
    }

    /**
     * Scope: Get active tags.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get tags by slug.
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
