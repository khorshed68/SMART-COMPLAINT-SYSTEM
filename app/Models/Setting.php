<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null): ?string
    {
        // Simple caching of settings
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('setting_key', $key)->first();
            return $setting ? $setting->setting_value : $default;
        });
    }

    /**
     * Set a setting value by key (upsert).
     */
    public static function set(string $key, ?string $value, string $group = 'general', string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_group' => $group,
                'description' => $description,
            ]
        );

        Cache::forget("setting.{$key}");
        Cache::forget("settings.group.{$group}");

        return $setting;
    }

    /**
     * Get all settings in a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return self::where('setting_group', $group)
                ->pluck('setting_value', 'setting_key')
                ->toArray();
        });
    }
}
