<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class WebsiteImages
{
    private const VERSION = 1;

    private static function source(?string $value): ?array
    {
        if (! $value || config('filesystems.disks.public.driver') !== 'local') {
            return null;
        }
        if (str_contains($value, '://') || str_starts_with($value, '//')) {
            if (parse_url($value, PHP_URL_HOST) !== parse_url(config('app.url'), PHP_URL_HOST)) {
                return null;
            }
            $value = parse_url($value, PHP_URL_PATH) ?: '';
        }
        $relative = preg_replace('#^/?storage/#', '', $value);
        if (! preg_match('#^[a-zA-Z0-9_/-]+\.(?:png|jpe?g|webp)$#i', $relative)
            || str_contains($relative, '..') || str_starts_with($relative, '/')
            || str_starts_with($relative, '_website/')) {
            return null;
        }
        $disk = Storage::disk('public');
        $root = realpath($disk->path(''));
        $path = realpath($disk->path($relative));
        if (! $root || ! $path || ! str_starts_with($path, $root.DIRECTORY_SEPARATOR) || ! is_file($path)) {
            return null;
        }
        return [$relative, $path, '_website/'.substr(hash('sha256', $relative), 0, 24)];
    }

    /** Read-only: rendering never resizes images or changes the original. */
    public static function attributes(?string $url): array
    {
        try {
            $source = self::source($url);
            if (! $source) {
                return [];
            }
            [$relative, $path, $directory] = $source;
            $disk = Storage::disk('public');
            if (! $disk->exists($directory.'/manifest.json')) {
                return [];
            }
            $data = json_decode($disk->get($directory.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
            if (($data['version'] ?? 0) !== self::VERSION || $data['size'] !== filesize($path) || $data['mtime'] !== filemtime($path)) {
                return [];
            }
            $variants = [];
            $fallback = null;
            foreach ($data['variants'] as $variant) {
                if (! str_starts_with($variant['path'], $directory.'/') || ! $disk->exists($variant['path'])) {
                    return [];
                }
                $variantUrl = $disk->url($variant['path']);
                $fallback ??= $variantUrl;
                $variants[] = $variantUrl.' '. $variant['width'].'w';
            }
            $variants[] = $disk->url($relative).' '.$data['width'].'w';
            return ['src' => $fallback ?? $disk->url($relative), 'width' => $data['width'], 'height' => $data['height'], 'srcset' => implode(', ', $variants)];
        } catch (\Throwable) {
            return [];
        }
    }

    /** Additional files only. Upload success never depends on this optimization. */
    public static function prepare(?string $url): bool
    {
        try {
            $source = self::source($url);
            if (! $source || ! function_exists('imagewebp')) {
                return false;
            }
            if (self::attributes($url)) {
                return true;
            }
            [$relative, $path, $directory] = $source;
            $info = @getimagesize($path);
            if (! $info || $info[0] * $info[1] > 16000000 || filesize($path) > 10 * 1024 * 1024) {
                return false;
            }
            // Never flatten animated WebP uploads.
            $contents = file_get_contents($path);
            if ($info[2] === IMAGETYPE_WEBP && str_contains($contents, 'ANIM')) {
                return false;
            }
            if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                $exif = @exif_read_data($path);
                if (is_array($exif) && ($exif['Orientation'] ?? 1) !== 1) {
                    return false;
                }
            }
            $disk = Storage::disk('public');
            $manager = new ImageManager(['driver' => 'gd']);
            $hash = substr(hash('sha256', $contents), 0, 16);
            $data = ['version' => self::VERSION, 'size' => filesize($path), 'mtime' => filemtime($path), 'width' => $info[0], 'height' => $info[1], 'variants' => []];
            foreach ([160, 320, 480, 640, 960] as $width) {
                if ($width >= $info[0]) {
                    continue;
                }
                $image = $manager->make($contents);
                $image->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $encoded = (string) $image->encode('webp', 90);
                $image->destroy();
                if (strlen($encoded) >= $data['size']) {
                    continue;
                }
                $destination = $directory.'/'.$hash.'-'.$width.'.webp';
                if (! $disk->put($destination, $encoded)) {
                    return false;
                }
                $data['variants'][] = ['path' => $destination, 'width' => $width];
            }
            // Publish the manifest only after every referenced file exists.
            return $disk->put($directory.'/manifest.json', json_encode($data, JSON_THROW_ON_ERROR));
        } catch (\Throwable) {
            return false;
        }
    }
}
