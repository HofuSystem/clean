<?php

namespace Core\Settings\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Settings\Models\Setting;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use ApiResponse;
    function __construct(protected SettingsService $settingsService) {}
    public function about()
    {
        $about = Setting::where('key', 'about_' . config('app.locale'))->first()?->value;
        return $this->returnData(trans('about'), ['status' => 'success', 'data' => ['about' => $about]]);
    }
    public function terms()
    {
        $terms = Setting::where('key', 'terms_' . config('app.locale'))->first()?->value;
        return $this->returnData(trans('terms'), ['status' => 'success', 'data' => ['terms' => $terms]]);
    }
    public function policy()
    {
        $policy = Setting::where('key', 'policy_' . config('app.locale'))->first()?->value;
        return $this->returnData(trans('policy'), ['status' => 'success', 'data' => ['policy' => $policy]]);
    }
    public function contact()
    {
        $data        = [
            "phone"    => SettingsService::getDataBaseSetting('phone'),
            "email"    => SettingsService::getDataBaseSetting('email'),
            "whatsapp" => SettingsService::getDataBaseSetting('whatsapp'),
            "social"   => [
                "facebook"  => SettingsService::getDataBaseSetting('facebook'),
                "twitter"   => SettingsService::getDataBaseSetting('twitter'),
                "instagram" => SettingsService::getDataBaseSetting('instagram'),
                "snap_chat" => SettingsService::getDataBaseSetting('snap_chat')
            ]
        ];
        return $this->returnData(trans('contact'), $data);
    }
    public function appSettings(Request $request)
    {
        $firstOrderMinPrice = SettingsService::getDataBaseSetting('first_order_min_price');
        $orderMinPrice = SettingsService::getDataBaseSetting('order_min_price');
        $user = auth('api')->user();
        if($user){
            $hasOrder = $user->orders()->whereNotIn('status',['cancel_payment','failed_payment','cancelled','failed'])->exists();
            if($hasOrder){
                $firstOrderMinPrice = $orderMinPrice;
            }
        }
        $data        = [
            'data'      => [
                //main delivery settings
                "delivery_charge"        => SettingsService::getDataBaseSetting('delivery_charge'),
                "free_delivery"          => SettingsService::getDataBaseSetting('free_delivery'),
                "order_min_price"        => $firstOrderMinPrice,
                "points_per_spent_riyal" => SettingsService::getDataBaseSetting('points_per_spent_riyal'),
                "riyal_per_point_redeem" => SettingsService::getDataBaseSetting('riyal_per_point_redeem'),
                "minium_points_to_use"   => SettingsService::getDataBaseSetting('minium_points_to_use'),
                "register_points"        => SettingsService::getDataBaseSetting('register_points'),
                "referral_points"        => SettingsService::getDataBaseSetting('referral_points'),
                "referral_riyals"        => SettingsService::getDataBaseSetting('referral_riyals'),
                "login_using"            => SettingsService::getDataBaseSetting('login_using'),
                "allowed_payment_methods" => SettingsService::getDataBaseSetting('allowed_payment_methods'),
                "multiple_payment_fees" => SettingsService::getDataBaseSetting('multiple_payment_fees'),
                "max_carpet_area"        => SettingsService::getDataBaseSetting('max_carpet_area'),
                "time_difference"        => [
                    'carpets'   => SettingsService::getDataBaseSetting('carpets_hours'),
                    'clothes'   => SettingsService::getDataBaseSetting('clothes_hours'),
                    'furniture' => SettingsService::getDataBaseSetting('furniture_hours'),
                ]
            ]
        ];
        return $this->returnData(trans('appSettings'), $data);
    }

    public function appConfig()
    {
        $data = [
            "maintenance_mode"                  => false,
            "force_update"                      => true,
            "clear_local_data"                  => true,
            "message"                           => "A new version is available. Please update your app.",
            "android"       => [
                "latest_version_name"           => "2.2.0",
                "latest_version_code"           => 35,
                "min_supported_version_code"    => 0
            ],
            "ios"           => [
                "latest_version_number"         => "2.2.0",
                "latest_build_number"           => 0,
                "min_supported_build_number"    => 0
            ],

        ];
        return $this->returnData(trans('appConfig'), $data);
    }
    public function appConfigNew()
    {
        $data = [
            "maintenance_mode"              => false,
            "message"                       => trans('A new version is available. Please update your app.'),
            "force_update"                  => true,
            "clear_local_data"              => false,
            "min_clear_local_data_version"  => "0.0.0",
            "force_clean_local_data"        => false,
            "force_clean_local_data_id"     => "",
            "android"   => [
                "latest_version"            => "3.0.4",
                "min_supported_version"     => "3.0.2"
            ],
            "ios"       => [
                "latest_version"            => "3.0.4",
                "min_supported_version"     => "3.0.4"
            ]
        ];
        return $this->returnData(trans('appConfig'), $data);
    }
}
