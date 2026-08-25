<?php

namespace Core\Notification\Services;

use Core\Users\Models\Device;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Core\Notification\Models\LastSentToToken;
use Core\Notification\Models\Notification;
use Illuminate\Support\Facades\DB;

class FCMService
{

    private $path = null;
    public function __construct() {}

    public static function getInstance(): self
    {
        return new static();
    }

    public  function getFcmConfigFromFile()
    {
        $fileBath = base_path('fcm.json');
       
        if (file_exists($fileBath)) {
            $content = file_get_contents($fileBath);
            return json_decode($content, true);
        }
        return [];
    }
    public function getAccessToken($config)
    {
        $client = new Client();
        $client->setAuthConfig($config);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        return  $client->fetchAccessTokenWithAssertion()['access_token'];
    }
    public function manageTopicSubscription(string $type = 'add', array $deviceTokens, string $topic, string $accessToken)
    {   
        $message = "manageTopicSubscription: $type: $topic: of ".count($deviceTokens)." tokens";
        app(TelegramNotificationService::class)->sendMessage('@itcleanstation',$message);
        $url    = ($type == "add") ? "https://iid.googleapis.com/iid/v1:batchAdd" : "https://iid.googleapis.com/iid/v1:batchRemove";
        $data   = [
            'to' => "/topics/" . $topic,
            'registration_tokens' => $deviceTokens
        ];

        $response = Http::withHeaders([
            'Authorization'     => "Bearer {$accessToken}",
            'Content-Type'      => 'application/json',
            'access_token_auth' => "true",
        ])->post($url, $data);
        return $response->json()['results'] ?? [];
    }

    public  function sendToTopic(string $topic, string $title, string $message, array $payload = [])
    {
        try {
            $fcmConfig      = $this->getFcmConfigFromFile();
            $bearerToken    = $this->getAccessToken($fcmConfig);
            $data = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body'  => $message,
                    ],
                    'data' => !empty($payload) ? $payload : null
                ]
            ];
            $url            = "https://fcm.googleapis.com/v1/projects/{$fcmConfig['project_id']}/messages:send";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$bearerToken}",
                'Content-Type' => 'application/json',
            ])->post($url, $data);
            app(TelegramNotificationService::class)->sendMessage('@itcleanstation',$response->body());
        } catch (\Exception $e) {
            report($e);
        }
    } // end of fcm_sender
    public   function sendToCustomTopic($notificationId,string $customTopic, array $tokens, string $title, string $message, array $payload = [])
    {
        try {
            if (empty($tokens)) {
                return;
            }
            $fcmConfig          = $this->getFcmConfigFromFile();
            $accessToken        = $this->getAccessToken($fcmConfig);
            $notWorkingTokens   = [];
            $workingTokens      = [];
            //remove old assign device to a topic form the languages
            LastSentToToken::where('topic', $customTopic)->get()->pluck('token')->chunk(1000)->each(function ($tokens) use ($accessToken, $customTopic) {
                $res= $this->manageTopicSubscription('remove', $tokens->toArray(), $customTopic, $accessToken);
            });
            //delete old records
            LastSentToToken::where('topic', $customTopic)->delete();


            // Split the tokens into chunks of 1000
            $notWorkingTokens   = [];
            $workingTokens      = [];
            $chunks             = array_chunk($tokens, 1000);
            foreach ($chunks as $chunk) {
                $registerTokensResult = $this->manageTopicSubscription('add', $chunk, $customTopic, $accessToken);
                foreach ($registerTokensResult as $index => $result) {
                    if (!empty($result)) {
                        $notWorkingTokens[] = $chunk[$index];
                    } else {
                        $workingTokens[] = ['token' => $chunk[$index], 'topic' => $customTopic,'created_at' => now(),'updated_at' => now()];
                    }
                }
            }
            //send fcm send to topic request
            $result = $this->sendToTopic( $customTopic, $title, $message, $payload);
            //create new records for last sent to tokens
            LastSentToToken::insert($workingTokens);

            $notificationTokens = array_map(function($token)use($title,$notificationId){
                return ['token' => $token['token'], 'topic' => $token['topic'], 'status' => 'success', 'title' => $title, 'notification_id' => $notificationId,'created_at' => now(),'updated_at' => now()];
            }, $workingTokens);

            $notWorKingNotificationTokens = array_map(function($token)use($title,$notificationId,$customTopic){
                return ['token' => $token, 'topic' => $customTopic, 'status' => 'failed', 'title' => $title, 'notification_id' => $notificationId,'created_at' => now(),'updated_at' => now()];
            }, $notWorkingTokens);
            DB::table('notification_tokens')->insert($notificationTokens);
            DB::table('notification_tokens')->insert($notWorKingNotificationTokens);

            $workingTokens = array_map(function($token){
                return $token['token'];
            }, $workingTokens);
            DB::table('users_notifications')
            ->join('devices','users_notifications.user_id','=','devices.user_id')
            ->whereIn('devices.device_token',$workingTokens)
            ->where('users_notifications.notifications_type',Notification::class)
            ->where('users_notifications.notifications_id',$notificationId)
            ->update([
                'status' => 'sent',
                'response' => 'sucess send to topic',
            ]);
            DB::table('users_notifications')
            ->join('devices','users_notifications.user_id','=','devices.user_id')
            ->whereIn('devices.device_token',$tokens)
            ->whereNotIn('devices.device_token',$workingTokens)
            ->where('users_notifications.notifications_type',Notification::class)
            ->where('users_notifications.notifications_id',$notificationId)
            ->update([
                'status' => 'failed',
                'response' => "not working tokens",
            ]);
            //delete all devices tokens that are not working
            $this->manageTopicSubscription('remove', $notWorkingTokens, $customTopic, $accessToken);
            Device::whereIn('device_token', $notWorkingTokens)->forceDelete();
        } catch (\Exception $e) {
            report($e);
            DB::table('users_notifications')
            ->join('devices','users_notifications.user_id','=','devices.user_id')
            ->whereIn('devices.device_token',$tokens)
            ->where('users_notifications.notifications_type',Notification::class)
            ->where('users_notifications.notifications_id',$notificationId)
            ->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);
        }
    
    } // end of fcm_sender
    public   function convertIntegersToStrings($array) {
        if(!is_array($array)){
            return ;
        }
        foreach ($array ?? [] as $key => $value) {
            if (is_array($value)) {
                // Recursively handle nested arrays
                $array[$key] = $this->convertIntegersToStrings($value);
            } elseif (is_int($value)) {
                // Convert integer to string
                $array[$key] = (string)$value;
            }
        }
        return $array;
    }
    public   function sendToDevice($userId ,$notificationId,string $token, string $title, string $message, array $payload = [])
    {
        try {
            $fcmConfig      = $this->getFcmConfigFromFile();
            $bearerToken    = $this->getAccessToken($fcmConfig);
            $payload        = $this->convertIntegersToStrings($payload);
            if(isset($payload['sender_data'])){
                unset($payload['sender_data']);
            }

            $data = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $message,
                    ],
                    'data' => !empty($payload) ? $payload : null
                ]
            ];

            $url            = "https://fcm.googleapis.com/v1/projects/{$fcmConfig['project_id']}/messages:send";
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$bearerToken}",
                'Content-Type' => 'application/json',
            ])->post($url, $data);
            if($response->successful()){
                $status = "sent";
                $response = $response->body();
            }else{
                $status = "failed";
                $response = $response->body();
            }
        } catch (\Throwable $e) {
            report($e);
            $status = "failed";
            $response = $e->getMessage();
        }
        
        if(isset($notificationId) and isset($userId)){
            DB::table('users_notifications')
            ->where('notifications_type',Notification::class)
            ->where('notifications_id',$notificationId)
            ->where('user_id',$userId)
            ->update([
                'status'   => $status,
                'response' => $response,
            ]);
        }
    } // end of fcm_sender

}
