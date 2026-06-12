<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, Translatable, SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'slug',
        'featured_image',
        'status',
        'published_at',
        'scheduled_at',
        'views_count',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    // Translatable fields
    protected $translatable = ['title', 'excerpt', 'content'];

    /**
     * Get the category this post belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author of this post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get all tags for this post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag')
            ->where('is_active', true)
            ->withTimestamps();
    }

    /**
     * Get all revisions of this post.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(PostRevision::class)->orderByDesc('revision_number');
    }

    /**
     * Get SEO metadata.
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Get affiliate links in this post.
     */
    public function affiliateLinks(): HasMany
    {
        return $this->hasMany(AffiliateLink::class)->where('is_active', true);
    }

    /**
     * Get the latest revision.
     */
    public function latestRevision(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PostRevision::class)->orderByDesc('revision_number');
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Publish this post.
     */
    public function publish(): self
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return $this;
    }

    /**
     * Archive this post.
     */
    public function archive(): self
    {
        $this->update(['status' => 'archived']);

        return $this;
    }

    /**
     * Scope: Get published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope: Get featured posts.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Get posts by category.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Get posts by author.
     */
    public function scopeByAuthor(Builder $query, int $authorId): Builder
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Scope: Get posts with a specific tag.
     */
    public function scopeWithTag(Builder $query, string $slug): Builder
    {
        return $query->whereHas('tags', function ($q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    /**
     * Scope: Get recent posts.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Get route key for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
