<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'clicks',
        'conversions',
        'earnings',
        'is_active',
    ];

    protected $casts = [
        'clicks' => 'integer',
        'conversions' => 'integer',
        'commission_rate' => 'float',
        'earnings' => 'float',
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
     * Get click events for this affiliate link.
     */
    public function clickEvents(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    /**
     * Get conversion events for this affiliate link.
     */
    public function conversionEvents(): HasMany
    {
        return $this->hasMany(AffiliateConversion::class);
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
    public function recordClick(array $metadata = []): self
    {
        $this->increment('clicks');
        $this->clickEvents()->create([
            'ip_address' => $metadata['ip_address'] ?? null,
            'user_agent' => $metadata['user_agent'] ?? null,
            'referrer' => $metadata['referrer'] ?? null,
            'clicked_at' => $metadata['clicked_at'] ?? now(),
        ]);

        return $this;
    }

    /**
     * Record a conversion.
     */
    public function recordConversion(array $metadata = []): self
    {
        $this->increment('conversions');
        $this->earnings = $this->calculateEarnings();
        $this->conversionEvents()->create([
            'amount' => $metadata['amount'] ?? ($this->commission_rate ? $this->commission_rate / 100 : 0),
            'ip_address' => $metadata['ip_address'] ?? null,
            'user_agent' => $metadata['user_agent'] ?? null,
            'referrer' => $metadata['referrer'] ?? null,
            'converted_at' => $metadata['converted_at'] ?? now(),
        ]);
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
