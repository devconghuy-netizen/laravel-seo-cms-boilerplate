<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['locale', 'translatable_type', 'translatable_id', 'key', 'value'];

    /**
     * Get the translatable model.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Get translations for a specific locale.
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope: Get translations by model type.
     */
    public function scopeForModel($query, string $modelType, int $modelId = null)
    {
        $query->where('translatable_type', $modelType);

        if ($modelId) {
            $query->where('translatable_id', $modelId);
        }

        return $query;
    }
}
