<?php
namespace Core\Settings\Services;

use Core\Admin\Services\DashboardService;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Settings\Models\Setting;
use Illuminate\Support\Facades\App;
class BootService{
    
    static function bootConfig(){
        $settings = [
            'dashboard_widgets' => [

            ],
            'dashboard_icons_left' => [
                // ["icon" =>"fa-solid fa-inbox", "title"=>trans('title of icon'), "url"=>route('dashboard.index')]

            ],
            'admin_settings' =>[
               'admin_login' =>[
               
                ],
            ],
            'dashboard_menu'        => DashboardService::getMenu(),
            'home_menu'             => DashboardService::getMenu('home-nav'),
        ];
        $apiSettings = [
            'allowed_register' => true
        ];
        try {
            $name = Setting::where('key','name_'.config('app.locale'))->first()?->value;
            $logo = Setting::where('key','logo')->first()?->value;
        } catch (\Throwable $th) {
           $logo = null;
           $name = null;
        }
        config()->set('backend-settings',$settings);
        config()->set('api-settings',$apiSettings);
        config()->set('app.name',$name);
        config()->set('app.logo',MediaCenterHelper::getImagesUrl($logo));
        config()->set('app.icon',MediaCenterHelper::getImagesUrl($logo));
        \Config::set('app.fallback_locale', LanguageService::getDefaultLocale());
        \Config::set('app.activeLangs',LanguageService::getActiveLangs());
        \Config::set('app.currentLocaleNative',LanguageService::getCurrentLocaleNative());
        \Config::set('app.currentLocaleDir',LanguageService::getCurrentLocaleDir());
        if (request()->hasHeader('Accept-Language')) {
            $locale = request()->header('Accept-Language');
            $supportedLocales = ['en', 'ar']; // Define your supported languages

            if (in_array($locale, $supportedLocales)) {
                App::setLocale($locale);
            }
        }
        return $settings;
    }
}