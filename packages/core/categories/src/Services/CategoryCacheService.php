<?php

namespace Core\Categories\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redis;

class CategoryCacheService
{
    /**
     * Flush all home, category, package, and product related API caches.
     * Works seamlessly across Database, Redis, File, and Array cache drivers.
     */
    public static function flush(): void
    {
        // 1. Tag-based flush for stores that support tags (e.g. Redis, Memcached)
        try {
            if (Cache::supportsTags()) {
                Cache::tags(['categories_api'])->flush();
            }
        } catch (\Throwable $e) {
            Log::warning('CategoryCacheService tag flush failed: ' . $e->getMessage());
        }

        // 2. Explicit key forgets for stores that don't support tags or for untagged keys
        try {
            $locales = ['ar', 'en'];
            foreach ($locales as $locale) {
                Cache::forget("home_economy_bags_{$locale}");
                Cache::forget("home_services_sales_{$locale}");
            }
            Cache::forget('categories_api');
        } catch (\Throwable $e) {
            Log::warning('CategoryCacheService key forget failed: ' . $e->getMessage());
        }

        // 3. Database cache table purging (default store: database)
        try {
            if (Schema::hasTable('cache')) {
                DB::table('cache')
                    ->where(function ($q) {
                        $q->where('key', 'like', '%home_%')
                          ->orWhere('key', 'like', '%api_category_%')
                          ->orWhere('key', 'like', '%api_package_%')
                          ->orWhere('key', 'like', '%api_services_%')
                          ->orWhere('key', 'like', '%categories_api%');
                    })
                    ->delete();
            }
        } catch (\Throwable $e) {
            Log::warning('CategoryCacheService DB cache flush failed: ' . $e->getMessage());
        }

        // 4. Redis pattern deletion if Redis is configured and active
        try {
            $defaultStore = config('cache.default');
            $driver = config("cache.stores.{$defaultStore}.driver", $defaultStore);
            if ($driver === 'redis') {
                $redis = Redis::connection(config("cache.stores.{$defaultStore}.connection", 'default'));
                $prefix = config('database.redis.options.prefix', '');
                $patterns = ['*home_*', '*api_category_*', '*api_package_*', '*api_services_*', '*categories_api*'];
                foreach ($patterns as $pattern) {
                    $keys = $redis->keys($prefix . $pattern);
                    if (!empty($keys)) {
                        foreach ($keys as $k) {
                            $redis->del($k);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore Redis connection errors if Redis server is not reachable
        }
    }
}
