<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = ['id'];

    /**
     * Get a setting value by key. JSON-typed values are decoded.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::rememberForever("setting:{$key}", fn () => static::where('key', $key)->first());

        if (! $setting) {
            return $default;
        }

        if ($setting->type === 'json') {
            return json_decode($setting->value, true) ?? $default;
        }

        return $setting->value ?? $default;
    }

    /**
     * Get a localized setting value ({"id":..,"en":..}) for the current locale.
     */
    public static function getLocalized(string $key, ?string $locale = null, mixed $default = null): mixed
    {
        $value = static::get($key);

        if (is_array($value)) {
            $locale = $locale ?? app()->getLocale();

            return $value[$locale] ?? $value['id'] ?? $default;
        }

        return $value ?? $default;
    }

    protected static function booted(): void
    {
        static::saved(fn (Setting $s) => Cache::forget("setting:{$s->key}"));
        static::deleted(fn (Setting $s) => Cache::forget("setting:{$s->key}"));
    }
}
