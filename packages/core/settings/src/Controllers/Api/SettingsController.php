<?php

namespace Core\Settings\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Settings\Models\Setting;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @group 4. General & Settings
 * @subgroup Settings
 */
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
                "clothes_allowed_payment_methods"   => SettingsService::getDataBaseSetting('clothes_allowed_payment_methods') ?? ["cash", "card", "points", "wallet"],
                "maid_allowed_payment_methods"      => SettingsService::getDataBaseSetting('maid_allowed_payment_methods') ?? ["cash", "card", "points", "wallet"],
                "services_allowed_payment_methods"  => SettingsService::getDataBaseSetting('services_allowed_payment_methods') ?? ["cash", "card", "points", "wallet"],
                "sales_allowed_payment_methods"     => SettingsService::getDataBaseSetting('sales_allowed_payment_methods') ?? ["cash", "card", "points", "wallet"],
                "multiple_payment_fees" => SettingsService::getDataBaseSetting('multiple_payment_fees'),
                "max_carpet_area"        => SettingsService::getDataBaseSetting('max_carpet_area'),
                "time_difference"        => [
                    'carpets'   => SettingsService::getDataBaseSetting('carpets_hours'),
                    'clothes'   => SettingsService::getDataBaseSetting('clothes_hours'),
                    'furniture' => SettingsService::getDataBaseSetting('furniture_hours'),
                ],
                "maid_scheduled"         => SettingsService::getDataBaseSetting('maid_scheduled') ?? [],
                "clothes_tab"            => (SettingsService::getDataBaseSetting('clothes_tab') ?? '1') == '1',
                "maid_tab"               => (SettingsService::getDataBaseSetting('maid_tab') ?? '1') == '1',
                "flowers_tab"            => (SettingsService::getDataBaseSetting('flowers_tab') ?? '1') == '1',
                "service_tab"            => (SettingsService::getDataBaseSetting('service_tab') ?? '1') == '1',
                "sales_tab"              => (SettingsService::getDataBaseSetting('sales_tab') ?? '1') == '1',
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
        $defaultMessage = trans('A new version is available. Please update your app.');
        $lang = app()->getLocale() === 'en' ? 'en' : 'ar';
        $localizedMessage = SettingsService::getDataBaseSetting('app_update_message_' . $lang);
        $legacyMessage = SettingsService::getDataBaseSetting('app_update_message');
        $message = (is_string($localizedMessage) && $localizedMessage !== '')
            ? $localizedMessage
            : ((is_string($legacyMessage) && $legacyMessage !== '') ? $legacyMessage : $defaultMessage);

        $data = [
            "maintenance_mode"              => (SettingsService::getDataBaseSetting('app_maintenance_mode') ?? '0') == '1',
            "message"                       => $message,
            "force_update"                  => (SettingsService::getDataBaseSetting('app_force_update') ?? '1') == '1',
            "clear_local_data"              => (SettingsService::getDataBaseSetting('app_clear_local_data') ?? '0') == '1',
            "min_clear_local_data_version"  => SettingsService::getDataBaseSetting('app_min_clear_local_data_version') ?? "0.0.0",
            "force_clean_local_data"        => (SettingsService::getDataBaseSetting('app_force_clean_local_data') ?? '0') == '1',
            "force_clean_local_data_id"     => SettingsService::getDataBaseSetting('app_force_clean_local_data_id') ?? "",
            "android"   => [
                "latest_version"            => SettingsService::getDataBaseSetting('android_latest_version') ?? "3.0.4",
                "min_supported_version"     => SettingsService::getDataBaseSetting('android_min_supported_version') ?? "3.0.2"
            ],
            "ios"       => [
                "latest_version"            => SettingsService::getDataBaseSetting('ios_latest_version') ?? "3.0.4",
                "min_supported_version"     => SettingsService::getDataBaseSetting('ios_min_supported_version') ?? "3.0.4"
            ]
        ];
        return $this->returnData(trans('appConfig'), $data);
    }
}
