<?php

namespace Core\Notification\Helpers;

use Core\Notification\Models\Notification;
use Core\Notification\Services\FCMService;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Support\Facades\Http;
use RM\Logs\Helpers\LogHelper;
use Google\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;

class NotificationsSender
{

   
    public static function whatsApp(array $receivers,string $title,string $message)
    {
        $rawMessage = $message;
        preg_match('/\d+/', $message, $matches);
        if (!empty($matches)) {
            $otpCode = $matches[0];
        } else {
            $otpCode = $message;
        }

        foreach ($receivers as $receiver) {
            $status = 'failed';
            $response = null;
            $failedToDeliver = false;

            try {
                $data = [
                    "token"         => "EABIy7zT1dfYBOZBdnpDucOqHsR7JBI574IUjyj4EXqNXwstBGgMDCPdyMEPoglQa78uVkNiAyfVY8t7xRZCt9TRVGdFcZAr7BbBO5M7vV2ZAENjI4ZCQSIUfkAfD96XDQvaPHNZA8Qnyp5OWezRZCwgoOU8H39K3aZCq23OlwJIsb5a543O0tV4ZBzkXob9pR07mT03s8a33ZC3Pj6eYQIyO2LFQpUaZCmWVnoHFQAwjKQg2ez9CvTyZB56rdzETo1B8",
                    "sender_id"     => "366046636597017",
                    "phone"         => $receiver->phone,
                    "template"      => "otp",
                    "param_1"       =>  $otpCode,
                    "url_button"    => "1561",
                ];
                $url    = "https://api.karzoun.app/CloudApi.php?".http_build_query($data);
                $res    = Http::timeout(5)->connectTimeout(3)->retry(1, 100)->post($url, $data);
                
                $responseBody = $res->json();
                $isFailed = !$res->successful() || (isset($responseBody['error']) && !empty($responseBody['error']));

                if(!$isFailed){
                    $status = 'sent';
                    $response = $responseBody ?? $res->body();
                }else{
                    $status = 'failed';
                    $response = $responseBody ?? $res->body();
                    $failedToDeliver = true;
                }
               
            } catch (\Throwable $e) {
                report($e);
                $status = 'failed';
                $response = $e->getMessage();
                $failedToDeliver = true;
            }

            if(isset($receiver->notificationId) and isset($receiver->id)){
                DB::table('users_notifications')
                ->updateOrInsert([
                    'notifications_type' => Notification::class,
                    'notifications_id' => $receiver->notificationId,
                    'user_id' => $receiver->id,
                ],[
                    'status'   => $status,
                    'response' => is_array($response) ? json_encode($response, JSON_UNESCAPED_UNICODE) : (string)$response,
                ]);
            }

            // Fallback: If WhatsApp fails or times out, send OTP via SMS automatically
            if ($failedToDeliver) {
                try {
                    self::sms([$receiver], $title, $rawMessage);
                } catch (\Throwable $smsEx) {
                    report($smsEx);
                }
            }
        }
    } // end of whats_App_Otp_sender
  
    public static function sms(array $receivers,string $title, string $message)
    { 

        foreach ($receivers as $receiver) {
            try {
                 $data = [
                    "userName" => 'anas dahbour', // settings('sms_username')
                    "userSender" => 'Clean-S', // settings('sms_sender')
                    "numbers" => $receiver->phone,
                    "apiKey" => '7ebb954f5bdf7ebce0b15b67456740e6',
                    //   "userName" => 'googan', // settings('sms_username')
                    //   "userSender" => 'cod-googan', // settings('sms_sender')
                    //   "numbers" => $mobile,
                    //   "apiKey" => '2355cfdd16f5b2c301a8ef246b4c8b2d',
                    "msg" => $message,
                ];
                $client = new GuzzleClient([
                    'timeout' => 5,
                    'connect_timeout' => 3,
                ]);
                $res = $client->request('POST', 'https://www.msegat.com/gw/sendsms.php', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Accept-Language' => 'ar'
                    ],
                    'body' => json_encode($data),
                ]);
                $body = $res->getBody()->getContents();
                
                $bodyJson = json_decode($body,true);
                if(isset($bodyJson['code']) && $bodyJson['code'] == 1){
                    $status = 'sent';
                    $response = $body;
                }else{
                    $status = 'failed';
                    $response =  $body; 
                }
            } catch (\Throwable $e) {
                report($e);
                $status = 'failed';
                $response = $e->getMessage();
            }
            if(isset($receiver->notificationId) and isset($receiver->id)){
                DB::table('users_notifications')
                ->updateOrInsert([
                    'notifications_type' => Notification::class,
                    'notifications_id' => $receiver->notificationId,
                    'user_id' => $receiver->id,
                ],[
                    'status'   => $status,
                    'response' => is_array($response) ? json_encode($response, JSON_UNESCAPED_UNICODE) : (string)$response,
                ]);
            }
        }

    } // end of sms_smart_sender
   
    public static  function fcm(array $receivers, string $title, string $message, array $payload = [])
    {
        try {
            $lastOne = end($receivers);
            $tokens = collect($receivers)->pluck('token')->toArray();
            FCMService::getInstance()->sendToCustomTopic($lastOne?->notificationId,'fcm-function-v5',$tokens,$title,$message,$payload);
        } catch (\Throwable $e) {
            report($e);
        }
    } // end of fcm_sender

    public static  function smtpSender(array $receivers, array $data)
    {
        try {
            $OgMsg            = $data['msg'];
            $OgTitle          = $data['title'];
            foreach ($receivers as $receiver) {
                $data['msg']    = ToolHelper::formatString($OgMsg, $receiver->toArray('smtp'));
                $data['title']  = ToolHelper::formatString($OgTitle, $receiver->toArray('smtp'));
                \Mail::to($receiver->email)->send(new \Core\Notification\Mail\SmtpMail( $data));
            }
            $status     = 'sent';
            $response   = 'Email sent successfully';

        } catch (\Exception $e) {
            report($e);
            $status = 'failed';
            $response = $e->getMessage();
        }
        if(isset($receiver->notificationId) and isset($receiver->id)){
            DB::table('users_notifications')
            ->updateOrInsert([
                'notifications_type' => Notification::class,
                'notifications_id' => $receiver->notificationId,
                'user_id' => $receiver->id,
            ],[
                'status'   => $status,
                'response' => $response,
            ]);
        }
    } // end of smtp_sender
}
