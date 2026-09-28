<?php

namespace Core\Notification\Helpers;

use Core\Settings\Helpers\ToolHelper;

class NotificationDataNormalizer
{
    /**
     * Safely normalize user IDs from various formats:
     * - PHP array of ints/strings
     * - JSON array string e.g. "[1, 2, 3]"
     * - Comma-separated string e.g. "1,2,3"
     * - Single scalar e.g. 5
     * - null / empty
     *
     * @param mixed $data
     * @return array
     */
    public static function toUserIds($data): array
    {
        if (empty($data)) {
            return [];
        }

        if (is_array($data)) {
            $ids = [];
            foreach ($data as $item) {
                if (is_numeric($item)) {
                    $ids[] = intval($item);
                }
            }
            return array_values(array_unique($ids));
        }

        if (is_int($data)) {
            return [$data];
        }

        if (is_string($data)) {
            $data = trim($data);
            if ($data === '') {
                return [];
            }

            // Check if valid JSON array
            if (ToolHelper::isJson($data)) {
                $decoded = json_decode($data, true);
                if (is_array($decoded)) {
                    $ids = [];
                    foreach ($decoded as $item) {
                        if (is_numeric($item)) {
                            $ids[] = intval($item);
                        }
                    }
                    return array_values(array_unique($ids));
                }
            }

            // Fallback to comma-separated string
            $parts = explode(',', $data);
            $ids = [];
            foreach ($parts as $part) {
                $trimmed = trim($part);
                if (is_numeric($trimmed)) {
                    $ids[] = intval($trimmed);
                }
            }
            return array_values(array_unique($ids));
        }

        return [];
    }

    /**
     * Safely normalize string lists (e.g. emails or phones):
     *
     * @param mixed $data
     * @return array
     */
    public static function toStringList($data): array
    {
        if (empty($data)) {
            return [];
        }

        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                if (is_string($item) || is_numeric($item)) {
                    $trimmed = trim((string)$item);
                    if ($trimmed !== '') {
                        $result[] = $trimmed;
                    }
                }
            }
            return array_values($result);
        }

        if (is_string($data)) {
            $data = trim($data);
            if ($data === '') {
                return [];
            }

            if (ToolHelper::isJson($data)) {
                $decoded = json_decode($data, true);
                if (is_array($decoded)) {
                    $result = [];
                    foreach ($decoded as $item) {
                        if (is_string($item) || is_numeric($item)) {
                            $trimmed = trim((string)$item);
                            if ($trimmed !== '') {
                                $result[] = $trimmed;
                            }
                        }
                    }
                    return array_values($result);
                }
            }

            $parts = explode(',', $data);
            $result = [];
            foreach ($parts as $part) {
                $trimmed = trim($part);
                if ($trimmed !== '') {
                    $result[] = $trimmed;
                }
            }
            return array_values($result);
        }

        if (is_numeric($data)) {
            return [(string)$data];
        }

        return [];
    }

    /**
     * Safely mask a device push token, exposing only the last 6 characters.
     * Raw tokens MUST never be sent to the browser or exported.
     *
     * @param string|null $token
     * @return string
     */
    public static function maskToken(?string $token): string
    {
        if (empty($token)) {
            return '—';
        }
        $len = strlen($token);
        if ($len <= 6) {
            return '***';
        }
        return '***' . substr($token, -6);
    }
}
