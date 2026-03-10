<?php

namespace Core\Settings\Services;

use Core\Admin\Services\DashboardService;
use Core\Settings\Helpers\ToolHelper;
use Core\Settings\Models\Setting;

class SettingsService
{

    protected static $allSettings = null;
    public static function getAllDataBaseSettings()
    {
        if (isset(self::$allSettings)) {
            return self::$allSettings;
        }
        self::$allSettings =  Setting::all()->keyBy('key')->map(function ($item) {
            return ToolHelper::isJson($item->value) ? json_decode($item->value) : $item->value;
        })->toArray();
        return self::$allSettings;
    }
    public static function getDataBaseSetting($key)
    {
       return self::getAllDataBaseSettings()[$key] ?? null;
    }
    public static function getDataBaseSettingImage($key)
    {
       return url('storage/'.self::getAllDataBaseSettings()[$key] ?? null);
    }
    public function saveSettings(array $settings)
    {
       foreach ($settings as $key => $value) {
           if(self::$allSettings){
               self::$allSettings[$key] = $value;
           }
            $value = (is_array($value) or is_object($value)) ? json_encode($value) : $value;
            Setting::updateOrCreate([
                'key'       => $key,
             
            ],['value' => $value,]);
       }
    }
}
