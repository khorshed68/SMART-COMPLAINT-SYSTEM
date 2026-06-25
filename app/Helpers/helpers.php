<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value.
     */
    function setting(string $key, $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}
