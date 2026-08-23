<?php

namespace Core\Settings\Services;

use Core\Admin\Services\DashboardService;
use Core\Settings\Helpers\ToolHelper;
use Core\Settings\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    protected static $allSettings = null;

    public static function getAllDataBaseSettings()
    {
        if (isset(self::$allSettings)) {
            return self::$allSettings;
        }

        self::$allSettings = Cache::rememberForever('app_database_settings', function () {
            return DB::table('settings')->get()->keyBy('key')->map(function ($item) {
                return ToolHelper::isJson($item->value) ? json_decode($item->value) : $item->value;
            })->toArray();
        });

        return self::$allSettings;
    }

    public static function getDataBaseSetting($key)
    {
       return self::getAllDataBaseSettings()[$key] ?? null;
    }

    public static function getDataBaseSettingImage($key)
    {
       return url('storage/'.(self::getAllDataBaseSettings()[$key] ?? ''));
    }

    public function saveSettings(array $settings)
    {
       foreach ($settings as $key => $value) {
            $encodedValue = (is_array($value) || is_object($value)) ? json_encode($value) : $value;
            Setting::updateOrCreate([
                'key' => $key,
            ], [
                'value' => $encodedValue,
            ]);
       }

       self::$allSettings = null;
       Cache::forget('app_database_settings');
    }
}
