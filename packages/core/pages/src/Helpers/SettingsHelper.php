<?php

namespace Core\Pages\Helpers;

class SettingsHelper
{
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // TODO: Connect this to your actual settings source (e.g., database, config, etc.)
        // Example stub:
        $settings = [
            'site_name' => 'Manara',
            'site_email' => 'info@manara.com',
        ];
        return $settings[$key] ?? $default;
    }
} 