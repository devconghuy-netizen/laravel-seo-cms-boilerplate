<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'locale',
        'title',
        'description',
        'keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_handle',
        'structured_data',
        'index',
        'follow',
    ];

    protected $casts = [
        'structured_data' => 'json',
        'index' => 'boolean',
        'follow' => 'boolean',
    ];

    /**
     * Get the parent SEO-able model.
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get robots meta value.
     */
    public function getRobotsAttribute(): string
    {
        $robots = [];

        if (!$this->index) {
            $robots[] = 'noindex';
        } else {
            $robots[] = 'index';
        }

        if (!$this->follow) {
            $robots[] = 'nofollow';
        } else {
            $robots[] = 'follow';
        }

        return implode(',', $robots);
    }

    /**
     * Get Open Graph meta tags as array.
     */
    public function getOpenGraphAttribute(): array
    {
        return [
            'og:title' => $this->og_title ?? $this->title,
            'og:description' => $this->og_description ?? $this->description,
            'og:image' => $this->og_image,
            'og:type' => $this->og_type,
            'og:url' => $this->canonical_url,
        ];
    }

    /**
     * Get Twitter meta tags as array.
     */
    public function getTwitterAttribute(): array
    {
        return [
            'twitter:card' => $this->twitter_card ?? 'summary',
            'twitter:title' => $this->title,
            'twitter:description' => $this->description,
            'twitter:creator' => $this->twitter_handle,
        ];
    }
}
