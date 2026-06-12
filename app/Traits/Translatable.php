<?php

namespace App\Traits;

use App\Models\Translation;

trait Translatable
{
    /**
     * Get all translations for this model.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get translation for a specific locale.
     */
    public function getTranslation(string $key, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->where('key', $key)
            ->value('value');
    }

    /**
     * Get all translations for a key across locales.
     */
    public function getTranslations(string $key): array
    {
        return $this->translations()
            ->where('key', $key)
            ->pluck('value', 'locale')
            ->toArray();
    }

    /**
     * Set a translation for a locale.
     */
    public function setTranslation(string $key, string $value, string $locale = null): self
    {
        $locale = $locale ?? app()->getLocale();

        $this->translations()->updateOrCreate(
            ['locale' => $locale, 'key' => $key],
            ['value' => $value]
        );

        return $this;
    }

    /**
     * Get translated attribute (returns for current locale).
     */
    public function getTranslatedAttribute(string $key): ?string
    {
        return $this->getTranslation($key);
    }

    /**
     * Magic getter for translated attributes.
     */
    public function __get($key)
    {
        // Check if this is a translatable attribute
        if (in_array($key, $this->translatable ?? [])) {
            return $this->getTranslation($key);
        }

        return parent::__get($key);
    }
}
