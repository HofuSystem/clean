<?php

namespace Core\Settings\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Models\Setting;
use Core\Settings\Requests\DeliveryChargesRequest;
use Core\Settings\Requests\SettingsRequest;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\User;

class SettingsController extends Controller
{
    use ApiResponse;
    function __construct(protected SettingsService $settingsService){}
    public function settings(){
        $title      = trans('settings index');
        $screen     = 'settings-index';
        $settings   = Setting::all()->keyBy('key')->map(function($settings){return $settings->value;}); 
        $users      = User::select('id','fullname','phone')->get();
        
        // Parse no order notifications from settings
        $noOrderNotifications = [];
        if (isset($settings['no_order_notifications'])) {
            $noOrderNotifications = json_decode($settings['no_order_notifications'], true) ?: [];
        }
        
        return view('settings::views.pages.settings.edit',compact('title','screen','settings','users','noOrderNotifications'));
    }

    public function settingsSave(SettingsRequest $request){
        try {
            $data  = $request->validated();
            
            // Process no_order_notifications and convert to JSON
            if (isset($data['no_order_notifications'])) {
                $data['no_order_notifications'] = $this->processNoOrderNotifications($data['no_order_notifications']);
            }
            
            // Process allowed_payment_methods and convert to JSON
            if (isset($data['allowed_payment_methods'])) {
                $data['allowed_payment_methods'] = json_encode($data['allowed_payment_methods']);
            }
            
            $data  = array_filter($data,function($value){
                return $value !== null;
            });
            $this->settingsService->saveSettings($data);
            return $this->returnSuccessMessage(trans('settings saved'));
        } catch (\Throwable $th) {
            dd($th);
            return $this->returnErrorMessage(trans('settings save error'),[],[],422);
        }
      
    }
    public function deliveryCharges(DeliveryChargesRequest $request){
        try {
            $data  = $request->validated();
            $this->settingsService->saveSettings($data);
            return $this->returnSuccessMessage(trans('settings saved'));
        } catch (\Throwable $th) {
            return $this->returnErrorMessage(trans('settings save error'),[],[],422);
        }
      
    }

    /**
     * Process no order notification messages and return as JSON
     */
    private function processNoOrderNotifications(array $notifications)
    {
        $processedNotifications = [];
        
        foreach ($notifications as $notificationData) {
            if (!empty($notificationData['days']) && !empty($notificationData['notification_title']) && !empty($notificationData['notification_body'])) {
                $processedNotifications[] = [
                    'days' => (int) $notificationData['days'],
                    'notification_title' => $notificationData['notification_title'],
                    'notification_body' => $notificationData['notification_body'],
                    'added_points' => (float) ($notificationData['added_points'] ?? 0), // Money amount
                    'money_expiry_days' => (int) ($notificationData['money_expiry_days'] ?? 30), // Money expiry days
                    'notes' => $notificationData['notes'] ?? '', // Notes field
                    'is_active' => isset($notificationData['is_active']) ? true : false,
                ];
            }
        }
        
        return json_encode($processedNotifications);
    }

    
}
