<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'user_id',
        'name',
        'original_filename',
        'mime_type',
        'size',
        'disk',
        'path',
        'media_type',
        'width',
        'height',
        'metadata',
        'alt_text',
        'description',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    /**
     * Get the user who uploaded this media.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full URL to the media.
     */
    public function getFullUrlAttribute(): string
    {
        return config('app.url') . '/storage/' . $this->path;
    }

    /**
     * Check if media is an image.
     */
    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    /**
     * Check if media is a video.
     */
    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanSizeAttribute(): string
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $i < count($units) && $size >= 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Scope: Get media by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('media_type', $type);
    }

    /**
     * Scope: Get media uploaded by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get images.
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->where('media_type', 'image');
    }

    /**
     * Scope: Get videos.
     */
    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('media_type', 'video');
    }
}
