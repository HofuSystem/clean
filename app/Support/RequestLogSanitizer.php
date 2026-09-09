<?php

namespace App\Support;

class RequestLogSanitizer
{
    public static function attributes(array $attributes): array
    {
        // Record operational metadata only; never retain the raw request body.
        return self::onlyScalars($attributes, [
            'id', 'order_id', 'category_id', 'city_id', 'district_id',
            'type', 'status', 'page', 'per_page', 'limit', 'offset',
        ]);
    }

    public static function headers(array $headers): array
    {
        $safe = [];
        $headers = array_change_key_case($headers, CASE_LOWER);
        foreach (['accept', 'accept-language', 'content-type', 'app-version', 'user-agent'] as $name) {
            $values = $headers[$name] ?? null;
            if (is_array($values)) {
                $safe[$name] = array_map(fn ($value) => mb_substr((string) $value, 0, 512), array_filter($values, 'is_scalar'));
            } elseif (is_scalar($values)) {
                $safe[$name] = mb_substr((string) $values, 0, 512);
            }
        }

        return $safe;
    }

    private static function onlyScalars(array $values, array $keys): array
    {
        $safe = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $values) && (is_scalar($values[$key]) || $values[$key] === null)) {
                $safe[$key] = is_string($values[$key]) ? mb_substr($values[$key], 0, 128) : $values[$key];
            }
        }

        return $safe;
    }
}
