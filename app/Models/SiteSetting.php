<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function values(array $defaults = []): array
    {
        $settings = static::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->all();

        return array_merge($defaults, array_filter($settings, fn ($value) => $value !== null));
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => blank($value) ? null : $value]
            );
        }
    }
}
