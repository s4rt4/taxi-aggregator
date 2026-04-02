<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    protected static ?array $cache = null;

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        if (static::$cache === null) {
            static::$cache = Cache::remember('site_settings', 3600, function () {
                return SiteSetting::pluck('value', 'key')->toArray();
            });
        }

        return static::$cache[$key] ?? $default;
    }

    /**
     * Clear the settings cache (call after updating settings).
     */
    public static function clearCache(): void
    {
        static::$cache = null;
        Cache::forget('site_settings');
    }
}
