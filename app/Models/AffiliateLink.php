<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'title',
        'description',
        'url',
        'slug',
        'affiliate_program',
        'product_id',
        'commission_rate',
        'type',
        'is_active',
    ];

    protected $casts = [
        'commission_rate' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Get the post this affiliate link belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Calculate total earnings for this link.
     */
    public function calculateEarnings(): float
    {
        if (!$this->conversions || !$this->commission_rate) {
            return 0;
        }

        return $this->conversions * ($this->commission_rate / 100);
    }

    /**
     * Get conversion rate percentage.
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->clicks === 0) {
            return 0;
        }

        return round(($this->conversions / $this->clicks) * 100, 2);
    }

    /**
     * Record a click on this affiliate link.
     */
    public function recordClick(): self
    {
        $this->increment('clicks');

        return $this;
    }

    /**
     * Record a conversion.
     */
    public function recordConversion(): self
    {
        $this->increment('conversions');
        $this->save();

        return $this;
    }

    /**
     * Scope: Get active links.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get links by program.
     */
    public function scopeByProgram(Builder $query, string $program): Builder
    {
        return $query->where('affiliate_program', $program);
    }

    /**
     * Scope: Get links by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Get route key for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
