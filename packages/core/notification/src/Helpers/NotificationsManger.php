<?php

namespace Core\Notification\Helpers;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Notification\Jobs\SendApps;
use Core\Notification\Models\Notification;
use Core\Notification\Jobs\SendMails;
use Core\Notification\Jobs\SendSMS;
use Core\Notification\Jobs\SendWhatsApp;
use Core\Notification\Services\FCMService;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\User;

class NotificationsManger
{

    public $sendTypes   = [];
    protected $emailsList;
    protected $phonesList;
    protected $tokensList;

    public $title;
    public $message;
    public $payload; // additional none required data for views

    protected $notification;

    public static function getInstance(): self
    {
        return new static();
    }


    function __construct()
    {
        //setup the list of the NotificationsReceiver
        $this->phonesList               = collect();
        $this->emailsList               = collect();
        $this->tokensList               = collect();
    }

    public function __get($property)
    {
        $method = 'get' . ucfirst($property);
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return $this->$property;
        }
    }

    // Magic Setter
    public function __set($property, $value)
    {
        $method = 'set' . ucfirst($property);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->$property = $value;
        }
        return $this;
    }
    // Setters

    function getNotificationUsers($notification)
    {
        return $this->getNotificationUserQuery($notification->for, $notification->for_data, $notification->register_from, $notification->register_to, $notification->orders_from, $notification->orders_to, $notification->orders_min, $notification->orders_max)->get();
    }
    function getNotificationUserQuery($for,$forData,$registerFrom,$registerTo,$ordersFrom,$ordersTo,$ordersMin,$ordersMax,$selectedArray = [])
    {
        $forData    = ToolHelper::isJson($forData) ? json_decode($forData) : $forData;
        $users      = User::when(!empty($selectedArray), function ($query) use ($selectedArray) {
            $query->select($selectedArray);
        })
        ->when($for === 'users', function ($query) use ($forData) {
            $query->when(is_array($forData) && !empty($forData), function ($query) use ($forData) {
                $query->whereIn('id', $forData);
            });
        })
        ->when($for === 'email', function ($query) use ($forData) {
            $emails = explode(',', $forData);
            $query->whereIn('email', $emails);
        })
        ->when($for === 'phone', function ($query) use ($forData) {
            $phones = explode(',', $forData);
            $phones = array_filter($phones, function ($phone) {
                return !empty($phone);
            });
            if(empty($phones)){
                $query->whereIn('phone', $phones);
            }
            $query->where(function ($q) use ($phones) {
                foreach ($phones as $phone) {
                    $q->orWhere('phone', 'like', '%' . trim($phone) . '%');
                }
            });
        })
        ->when(isset($registerFrom), function ($query) use ($registerFrom) {
            $query->where('created_at', '>=', $registerFrom);
        })
        ->when(isset($registerTo), function ($query) use ($registerTo) {
            $query->where('created_at', '<=', $registerTo);
        })
        ->when(
            isset($ordersFrom) || isset($ordersTo) || isset($ordersMin) || isset($ordersMax),
            function ($query) use ($ordersFrom, $ordersTo) {
                $query->withCount(['orders as filtered_orders_count' => function ($q) use ($ordersFrom, $ordersTo) {
                    if (isset($ordersFrom)) {
                        $q->where('created_at', '>=', $ordersFrom);
                    }
                    if (isset($ordersTo)) {
                        $q->where('created_at', '<=', $ordersTo);
                    }
                }])
                ->whereHas('orders', function ($q) use ($ordersFrom, $ordersTo) {
                    if (isset($ordersFrom)) {
                        $q->where('created_at', '>=', $ordersFrom);
                    }
                    if (isset($ordersTo)) {
                        $q->where('created_at', '<=', $ordersTo);
                    }
                });
            }
        )
        ->when(isset($ordersMin), function ($query) use ($ordersMin) {
            $query->having('filtered_orders_count', '>=', $ordersMin);
        })
        ->when(isset($ordersMax), function ($query) use ($ordersMax) {
            $query->having('filtered_orders_count', '<=', $ordersMax);
        });

        return $users;
    }
    function getNotificationUsersDevices($users)
    {
        $ids                = $users->pluck('id')->toArray();
        $tokensList         =   User::select('users.id','fullname', 'phone', 'email', 'device_token')
            ->join('devices', 'devices.user_id', '=', 'users.id')
            ->whereIn('user_id', $ids)
            ->whereNotNull('devices.device_token')
            ->get();
        return $tokensList;
    }

    function sendNotification(Notification $notification)
    {

        $users                  = $this->getNotificationUsers($notification);
        $this->notification     = $notification;
        $this->sendTypes        = json_decode($notification->types);

        if (array_intersect(['sms', 'whats_app', 'email'], $this->sendTypes)) {
            foreach ($users as $user) {
                $notificationsReceiver  = new NotificationsReceiver($user->id, $user->fullname,  $user->email, $user->phone, $user->device_token, $notification->id);
                if (in_array('sms', $this->sendTypes)) {
                    $this->phonesList->push($notificationsReceiver);
                }
                if (in_array('whats_app', $this->sendTypes)) {
                    $this->phonesList->push($notificationsReceiver);
                }
                if (in_array('email', $this->sendTypes)) {
                    $this->emailsList->push($notificationsReceiver);
                }
            }
        }

        if (in_array('apps', $this->sendTypes)) {
            $tokensList         =  $this->getNotificationUsersDevices($users);
            foreach ($tokensList as $user) {
                $notificationsReceiver  = new NotificationsReceiver($user->id,$user->fullname,  $user->email, $user->phone, $user->device_token,$notification->id);
                $this->tokensList->push($notificationsReceiver);
            }
        }
        if (str_contains($this->notification->for, 'email')) {
            $emails = explode(',', $notification->for_data);
            foreach ($emails as $email) {
                $emailListItem          = $this->emailsList->where('email', $email)->first();
                if ($emailListItem) {
                    continue;
                }
                $user                   = $users->where('email', $email)->first();
                $notificationsReceiver  = new NotificationsReceiver($user?->id ?? null, $user?->fullname ?? $email,  $user?->email ?? $email, $user?->phone ?? $email, $user?->device_token ?? $email, $notification->id);
                $this->emailsList->push($notificationsReceiver);
            }
        }
        if (str_contains($this->notification->for, 'phone') || str_contains($this->notification->for, 'whats_app')) {
            $phones = explode(',', $notification->for_data);
            foreach ($phones as $phone) {
                $phoneListItem          = $this->phonesList->filter(function ($user) use ($phone) {
                    return str_contains($user->phone, $phone);
                })->first();
                if ($phoneListItem) {
                    continue;
                }

                $user                   = $users->filter(function ($user) use ($phone) {
                    return str_contains($user->phone, $phone);
                })->first();
                $notificationsReceiver  = new NotificationsReceiver($user?->id ?? null, $user?->fullname ?? $phone,  $user?->email ?? $phone, $user?->phone ?? $phone, $user?->device_token ?? $phone, $notification->id);
                $this->phonesList->push($notificationsReceiver);
            }
        }
        // settings
        $this->title    = $notification->title;
        $this->message  = $notification->body;
        if ($notification->payload and !empty($notification->payload)) {
            $this->payload = ToolHelper::isJson($notification->payload)
                ?   json_decode($notification->payload, true)
                :   ['payload' => $notification->payload];
            if (isset($notification->media) and !empty($notification->media)) {
                $this->payload['media']  =  url($notification->media);
            }
        } else {
            $this->payload  = (isset($notification->media) and !empty($notification->media)) ? [
                'media' => MediaCenterHelper::getImagesUrl($notification->media)
            ] : [];
        }
        $ids  = $users->pluck('id')->toArray();
        $notification->users()->sync($ids);
        $this->send();
    }

    function send()
    {
        foreach ($this->sendTypes as $key  => $sendType) {
            $functionName = 'send' . \Str::ucfirst(\Str::camel($sendType));
            if (method_exists($this, $functionName)) {
                try {
                    $this->$functionName();
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }


    function sendWhatsApp()
    {
        if ($this->phonesList->isEmpty()) {
            return;
        }
        $total  = $this->phonesList->count();
        $sent   = 3;
        $max    = 10;
        $patch  = $this->phonesList->take($sent)->toArray();
        NotificationsSender::whatsApp($patch, $this->title, $this->message);

        while ($total > $sent) {
            $phones = $this->phonesList->skip($sent)->take($max)->toArray();
            dispatch(new SendWhatsApp($phones, $this->title, $this->message));
            $sent      += $max;
        }
    } // end of send whatsapp

    function sendSms()
    {
        if ($this->phonesList->isEmpty()) {
            return;
        }

        $total  = $this->phonesList->count();
        $sent   = 3;
        $max    = 10;
        $patch  = $this->phonesList->take($sent)->toArray();
        NotificationsSender::sms($patch, $this->title, $this->message);

        while ($total > $sent) {
            $phones = $this->phonesList->skip($sent)->take($max)->toArray();
            dispatch(new SendSMS($phones, $this->title, $this->message));
            $sent      += $max;
        }
    } // end of send_sms
    function sendEmail()
    {
        if ($this->emailsList->isEmpty()) {
            return;
        }
        $total  = $this->emailsList->count();
        $sent   = 3;
        $max    = 10;
        $patch  = $this->emailsList->take($sent)->toArray();
        NotificationsSender::smtpSender($patch, ['title' => $this->title, 'msg' => $this->message]);

        while ($total > $sent) {
            $emails = $this->emailsList->skip($sent)->take($max)->toArray();
            dispatch(new SendMails($emails, $this->title, $this->message));
            $sent      += $max;
        }
    } // end of send_email

    //send to mobile app with fcm
    function sendApps()
    {
        if ($this->tokensList->isEmpty()) {
            return;
        }
        $total  = $this->tokensList->count();

        if ($total <= 5) {
            foreach ($this->tokensList as $receiver) {
                FCMService::getInstance()->sendToDevice($receiver->id, $receiver->notificationId, $receiver->token, $this->title, $this->message, $this->payload);
            }
        } elseif ($total <= 100) {
            $patch  = $this->tokensList->pluck('token')->toArray();
            FCMService::getInstance()->sendToCustomTopic($this->tokensList->first()?->notificationId,'under-100-function-v5', $patch, $this->title, $this->message, $this->payload);
        } else {
            dispatch(new SendApps($this->tokensList->toArray(), $this->title, $this->message));
        }
    } // end of send_apps


}
