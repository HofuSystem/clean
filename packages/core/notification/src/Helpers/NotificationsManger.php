<?php

namespace Core\Notification\Helpers;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Notification\Jobs\SendApps;
use Core\Notification\Jobs\SendFcmBatchJob;
use Core\Notification\Jobs\SendMails;
use Core\Notification\Jobs\SendSMS;
use Core\Notification\Jobs\SendWhatsApp;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Services\FCMService;
use Core\Notification\Services\RecipientEligibilityService;
use Core\Notification\Services\TelegramNotificationService;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationsManger
{
    public $sendTypes = [];
    protected $phonesList;
    protected $emailsList;
    protected $tokensList;

    public $title;
    public $message;
    public $payload; // additional non-required data for views

    protected $notification;

    public static function getInstance(): self
    {
        return new static();
    }

    public function __construct()
    {
        $this->phonesList = collect();
        $this->emailsList = collect();
        $this->tokensList = collect();
    }

    public function __get($property)
    {
        $method = 'get' . ucfirst($property);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return $this->$property;
    }

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

    public function getNotificationUsers($notification)
    {
        return $this->getNotificationUserQuery(
            $notification->for,
            $notification->for_data,
            $notification->register_from,
            $notification->register_to,
            $notification->orders_from,
            $notification->orders_to,
            $notification->orders_min,
            $notification->orders_max,
            [],
            $notification->purpose
        )->get();
    }

    public function getNotificationUserQuery(
        $for,
        $forData,
        $registerFrom,
        $registerTo,
        $ordersFrom,
        $ordersTo,
        $ordersMin,
        $ordersMax,
        $selectedArray = [],
        $purpose = null
    ) {
        $users = User::when(!empty($selectedArray), function ($query) use ($selectedArray) {
            $query->select($selectedArray);
        });

        // Role filtering: only restrict to 'client' if purpose is explicitly 'marketing'
        if ($purpose === 'marketing') {
            $users->whereHas('roles', function ($q) {
                $q->where('name', 'client');
            })->where('is_active', 1);
        }

        // Recipient filters using safe normalizer (prevents explode TypeError)
        $users->when($for === 'users', function ($query) use ($forData) {
            $userIds = NotificationDataNormalizer::toUserIds($forData);
            if (!empty($userIds)) {
                $query->whereIn('id', $userIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        })
        ->when($for === 'email', function ($query) use ($forData) {
            $emails = NotificationDataNormalizer::toStringList($forData);
            if (!empty($emails)) {
                $query->whereIn('email', $emails);
            } else {
                $query->whereRaw('1 = 0');
            }
        })
        ->when($for === 'phone', function ($query) use ($forData) {
            $phones = NotificationDataNormalizer::toStringList($forData);
            if (!empty($phones)) {
                $query->where(function ($q) use ($phones) {
                    foreach ($phones as $phone) {
                        $q->orWhere('phone', 'like', '%' . $phone . '%');
                    }
                });
            } else {
                $query->whereRaw('1 = 0');
            }
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

    /**
     * Legacy method preserved for compatibility.
     * Selects all active non-invalid devices for the given users (not just MAX(id)).
     */
    public function getNotificationUsersDevices($users)
    {
        $ids = $users->pluck('id')->toArray();
        return User::select('users.id', 'fullname', 'phone', 'email', 'devices.device_token', 'devices.id as device_id')
            ->join('devices', 'devices.user_id', '=', 'users.id')
            ->whereIn('users.id', $ids)
            ->whereNull('devices.deleted_at')
            ->whereNotNull('devices.device_token')
            ->where('devices.token_status', '!=', 'invalid')
            ->get();
    }

    public function sendNotification(Notification $notification)
    {
        $this->notification = $notification;
        $this->sendTypes = is_string($notification->types) ? (json_decode($notification->types, true) ?: []) : (array)$notification->types;

        // Settings / content
        $this->title = $notification->title;
        $this->message = $notification->body;
        if ($notification->payload && !empty($notification->payload)) {
            $this->payload = ToolHelper::isJson($notification->payload)
                ? json_decode($notification->payload, true)
                : ['payload' => $notification->payload];
            if (isset($notification->media) && !empty($notification->media)) {
                $this->payload['media'] = url($notification->media);
            }
        } else {
            $this->payload = (!empty($notification->media)) ? [
                'media' => MediaCenterHelper::getImagesUrl($notification->media)
            ] : [];
        }

        // Use RecipientEligibilityService to resolve audience & eligibility
        $eligibilityService = app(RecipientEligibilityService::class);
        $evaluation = $eligibilityService->evaluateEligibility($notification);

        $targetedUsers = $evaluation['targeted_users'];
        $targetedCount = $evaluation['targeted_users_count'];
        $eligibleUsersCount = $evaluation['eligible_users_count'];
        $eligibleDevices = $evaluation['eligible_devices'];
        $userEligibility = $evaluation['user_eligibility'];

        // Snapshot initial campaign metrics
        $notification->update([
            'processing_status' => 'processing',
            'started_at' => now(),
            'targeted_users_count' => $targetedCount,
            'eligible_users_count' => $eligibleUsersCount,
            'eligible_devices_count' => count($eligibleDevices),
        ]);

        // Sync users_notifications pivot for targeted users
        $targetedIds = $targetedUsers->pluck('id')->toArray();
        $notification->users()->sync($targetedIds);

        // Update eligibility status per user in users_notifications (polymorphic safe)
        foreach ($userEligibility as $userId => $eligibility) {
            DB::table('users_notifications')
                ->where('notifications_type', Notification::class)
                ->where('notifications_id', $notification->id)
                ->where('user_id', $userId)
                ->update([
                    'eligibility_status' => $eligibility['status'],
                    'eligibility_reason' => $eligibility['reason'],
                    'status' => $eligibility['status'] === 'eligible' ? 'pending' : 'failed',
                    'response' => $eligibility['reason'] ?: 'pending device dispatch',
                ]);
        }

        // Setup receivers for SMS, WhatsApp, Email
        if (array_intersect(['sms', 'whats_app', 'email'], $this->sendTypes)) {
            foreach ($targetedUsers as $user) {
                $notificationsReceiver = new NotificationsReceiver($user->id, $user->fullname, $user->email, $user->phone, null, $notification->id);
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

        // Additional email/phone targets from for_data if specified
        if (str_contains((string)$this->notification->for, 'email')) {
            $emails = NotificationDataNormalizer::toStringList($notification->for_data);
            foreach ($emails as $email) {
                if ($this->emailsList->where('email', $email)->isEmpty()) {
                    $this->emailsList->push(new NotificationsReceiver(null, $email, $email, $email, null, $notification->id));
                }
            }
        }

        if (str_contains((string)$this->notification->for, 'phone') || str_contains((string)$this->notification->for, 'whats_app')) {
            $phones = NotificationDataNormalizer::toStringList($notification->for_data);
            foreach ($phones as $phone) {
                if ($this->phonesList->where('phone', $phone)->isEmpty()) {
                    $this->phonesList->push(new NotificationsReceiver(null, $phone, $phone, $phone, null, $notification->id));
                }
            }
        }

        // Dispatch Non-Apps channels (WhatsApp, SMS, Email)
        $this->dispatchNonAppChannels();

        // Dispatch Apps channel (FCM Push)
        if (in_array('apps', $this->sendTypes)) {
            if (config('notification.direct_fcm_enabled', false)) {
                $this->dispatchAppPushes($eligibleDevices);
            } else {
                $this->sendLegacyApps();
            }
        } else {
            // If apps was not selected, complete immediately
            $notification->update([
                'processing_status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Dispatch Apps push notifications directly in batches.
     */
    protected function dispatchAppPushes(array $eligibleDevices): void
    {
        $notification = $this->notification;
        $payload = is_array($notification->payload) ? $notification->payload : json_decode($notification->payload ?? '{}', true);
        if (empty($payload['delivery_channel'])) {
            $payload['delivery_channel'] = 'direct_fcm';
            $notification->update(['payload' => json_encode($payload)]);
        }
        $totalDevices = count($eligibleDevices);

        if ($totalDevices === 0) {
            $notification->update([
                'processing_status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        // Pre-insert queued token records so admin dashboard immediately reflects queued devices
        $now = now();
        $tokenInserts = [];
        foreach ($eligibleDevices as $dev) {
            $tokenInserts[] = [
                'topic' => 'direct',
                'token' => $dev['token'],
                'status' => 'queued',
                'title' => (string)$notification->title,
                'notification_id' => $notification->id,
                'user_id' => $dev['user_id'],
                'device_id' => $dev['device_id'],
                'installation_id' => $dev['installation_id'] ?? null,
                'platform' => $dev['platform'] ?? null,
                'attempts' => 0,
                'queued_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Chunk pre-inserts to avoid oversized SQL queries
        foreach (array_chunk($tokenInserts, 500) as $chunk) {
            DB::table('notification_tokens')->insertOrIgnore($chunk);
        }

        // Fetch inserted token IDs mapped to (device_id, token) to provide explicit notification_token_id in Job payload
        $insertedTokens = DB::table('notification_tokens')
            ->where('notification_id', $notification->id)
            ->select(['id', 'device_id', 'token'])
            ->get();

        $tokenMap = [];
        foreach ($insertedTokens as $tok) {
            $tokenMap[($tok->device_id ?? '') . '#' . $tok->token] = $tok->id;
        }

        foreach ($eligibleDevices as &$dev) {
            $key = ($dev['device_id'] ?? '') . '#' . ($dev['token'] ?? '');
            if (isset($tokenMap[$key])) {
                $dev['notification_token_id'] = $tokenMap[$key];
            }
        }
        unset($dev);

        // Small batch (<= 5 devices e.g. transactional order push): send synchronously
        if ($totalDevices <= 5) {
            FCMService::getInstance()->sendBatchDirect($notification, $eligibleDevices);
            $notification->update([
                'processing_status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        // Larger campaigns: dispatch batches of 50 devices to the queue via Bus::batch
        $deviceChunks = array_chunk($eligibleDevices, 50);
        $batchJobs = [];
        foreach ($deviceChunks as $chunk) {
            $batchJobs[] = new SendFcmBatchJob($notification, $chunk);
        }

        Bus::batch($batchJobs)
            ->name('Notification-' . $notification->id)
            ->allowFailures()
            ->finally(function () use ($notification) {
                $notification->update([
                    'processing_status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Send aggregated Telegram completion alert
                try {
                    $teleService = app(TelegramNotificationService::class);
                    $refreshed = $notification->fresh();
                    $msg = "ًں“¢ *Clean Station Campaign Completed*\n"
                         . "ID: #{$refreshed->id}\n"
                         . "Title: {$refreshed->title}\n"
                         . "Targeted: {$refreshed->targeted_users_count}\n"
                         . "Eligible Users: {$refreshed->eligible_users_count}\n"
                         . "Eligible Devices: {$refreshed->eligible_devices_count}\n"
                         . "Accepted by FCM: {$refreshed->accepted_by_fcm_count}\n"
                         . "Permanent Failed: {$refreshed->permanent_failed_count}\n"
                         . "Transient Failed: {$refreshed->transient_failed_count}";
                    $teleService->sendMessage('@itcleanstation', $msg);
                } catch (\Throwable $e) {
                    report($e);
                }
            })
            ->dispatch();
    }


    /**
     * Legacy topic / IID path preserved for safe rollback without migration rollbacks.
     * Active whenever config("notification.direct_fcm_enabled") is false.
     */
    protected function sendLegacyApps(): void
    {
        $payload = is_array($this->notification->payload) ? $this->notification->payload : json_decode($this->notification->payload ?? '{}', true);
        if (empty($payload['delivery_channel'])) {
            $payload['delivery_channel'] = 'legacy_topic';
            $this->notification->update(['payload' => json_encode($payload)]);
        }
        $devices = $this->getNotificationUsersDevices($this->notification->users);
        $this->tokensList = collect();
        foreach ($devices as $dev) {
            $this->tokensList->push(new NotificationsReceiver(
                $dev->id,
                $dev->fullname,
                $dev->email,
                $dev->phone,
                $dev->device_token,
                $this->notification->id,
                $dev->device_id
            ));
        }

        $targetedIds = $this->notification->users()->pluck('users.id')->toArray();
        $userIdsWithTokens = $this->tokensList->pluck('id')->toArray();
        $userIdsWithoutTokens = array_diff($targetedIds, $userIdsWithTokens);

        if (!empty($userIdsWithoutTokens)) {
            DB::table('users_notifications')
                ->where('notifications_id', $this->notification->id)
                ->where('notifications_type', Notification::class)
                ->where('status', 'pending')
                ->whereIn('user_id', $userIdsWithoutTokens)
                ->update([
                    'status' => 'failed',
                    'response' => 'No device token'
                ]);
        }

        if ($this->tokensList->isEmpty()) {
            $this->notification->update([
                'processing_status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        $total = $this->tokensList->count();
        if ($total <= 1000 && $total > 100) {
            $patches = $this->tokensList->pluck('token')->chunk(1000);
            foreach ($patches as $patch) {
                FCMService::getInstance()->sendToCustomTopic(
                    $this->tokensList->first()?->notificationId,
                    'under-1000-function-v5',
                    $patch->toArray(),
                    $this->title,
                    $this->message,
                    $this->payload
                );
            }
        } elseif ($total <= 100) {
            $patch = $this->tokensList->pluck('token')->toArray();
            FCMService::getInstance()->sendToCustomTopic(
                $this->tokensList->first()?->notificationId,
                'under-100-function-v5',
                $patch,
                $this->title,
                $this->message,
                $this->payload
            );
        } else {
            dispatch(new SendApps($this->tokensList->toArray(), $this->title, $this->message));
        }

        $this->notification->update([
            'processing_status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    protected function dispatchNonAppChannels(): void
    {
        $this->phonesList = $this->phonesList->unique('phone');
        $this->emailsList = $this->emailsList->unique('email');

        if (in_array('whats_app', $this->sendTypes)) {
            $this->sendWhatsApp();
        }
        if (in_array('sms', $this->sendTypes)) {
            $this->sendSms();
        }
        if (in_array('email', $this->sendTypes)) {
            $this->sendEmail();
        }
    }

    public function resendToUsers(Notification $notification, $users)
    {
        $this->notification = $notification;
        $this->sendTypes = is_string($notification->types) ? (json_decode($notification->types, true) ?: []) : (array)$notification->types;
        $this->title = $notification->title;
        $this->message = $notification->body;

        $userIds = $users instanceof \Illuminate\Support\Collection ? $users->pluck('id')->toArray() : (array)$users;
        $devices = Device::whereIn('user_id', $userIds)
            ->whereNotNull('device_token')
            ->where('token_status', '!=', 'invalid')
            ->where('notification_permission', '!=', 'denied')
            ->get();

        $eligibleDevices = [];
        $seen = [];
        foreach ($devices as $dev) {
            if (!isset($seen[$dev->device_token])) {
                $seen[$dev->device_token] = true;
                $eligibleDevices[] = [
                    'device_id' => $dev->id,
                    'user_id' => $dev->user_id,
                    'token' => $dev->device_token,
                    'platform' => $dev->type,
                    'installation_id' => $dev->installation_id,
                    'app_context' => $dev->app_context,
                ];
            }
        }

        if (!empty($eligibleDevices)) {
            if (config('notification.direct_fcm_enabled', false)) {
                $this->dispatchAppPushes($eligibleDevices);
            } else {
                $this->sendLegacyApps();
            }
        }
    }

    public function sendWhatsApp()
    {
        if ($this->phonesList->isEmpty()) {
            return;
        }
        $total = $this->phonesList->count();
        $sent = 3;
        $max = 10;
        $patch = $this->phonesList->take($sent)->toArray();
        NotificationsSender::whatsApp($patch, $this->title, $this->message);

        while ($total > $sent) {
            $phones = $this->phonesList->skip($sent)->take($max)->toArray();
            dispatch(new SendWhatsApp($phones, $this->title, $this->message));
            $sent += $max;
        }
    }

    public function sendSms()
    {
        if ($this->phonesList->isEmpty()) {
            return;
        }
        $total = $this->phonesList->count();
        $sent = 3;
        $max = 10;
        $patch = $this->phonesList->take($sent)->toArray();
        NotificationsSender::sms($patch, $this->title, $this->message);

        while ($total > $sent) {
            $phones = $this->phonesList->skip($sent)->take($max)->toArray();
            dispatch(new SendSMS($phones, $this->title, $this->message));
            $sent += $max;
        }
    }

    public function sendEmail()
    {
        if ($this->emailsList->isEmpty()) {
            return;
        }
        $total = $this->emailsList->count();
        $sent = 3;
        $max = 10;
        $patch = $this->emailsList->take($sent)->toArray();
        NotificationsSender::smtpSender($patch, ['title' => $this->title, 'msg' => $this->message]);

        while ($total > $sent) {
            $emails = $this->emailsList->skip($sent)->take($max)->toArray();
            dispatch(new SendMails($emails, $this->title, $this->message));
            $sent += $max;
        }
    }
}
