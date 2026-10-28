<?php

namespace Core\Info\Services;

use Core\Info\Models\CoverageNotification;
use Core\Notification\Helpers\NotificationsManger;
use Core\Users\Models\User;

class CoverageNotificationService
{
    /**
     * Subscribe a user to coverage notifications for a district or city.
     */
    public static function subscribe(
        int $userId,
        ?int $cityId,
        ?int $districtId,
        string $type = 'resume',
        ?int $addressId = null
    )
    {
        $identity = [
            'user_id'     => $userId,
            'address_id'  => $addressId,
            'city_id'     => $cityId,
            'district_id' => $districtId,
        ];

        return CoverageNotification::updateOrCreate($identity, [
            'address_id' => $addressId,
            'type'       => $type,
            'status'     => 'pending',
        ]);
    }

    /**
     * Trigger notifications when a district or city becomes active.
     */
    public static function notifySubscribersOnActivation(?int $cityId = null, ?int $districtId = null)
    {
        $query = CoverageNotification::where('status', 'pending');

        if ($districtId) {
            $query->where('district_id', $districtId);
        } elseif ($cityId) {
            $query->where('city_id', $cityId)->whereNull('district_id');
        } else {
            return;
        }

        $subscriptions = $query->with('user')->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $userIds = $subscriptions->pluck('user_id')->unique()->filter()->toArray();
        $users = User::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            return;
        }

        $sent = false;

        $title   = trans('Good news! Service is now active in your area!');
        $message = trans('Great news! Service is now active in your location. We are excited to serve you!');

        try {
            NotificationsManger::getInstance()
                ->setUsers($users)
                ->setTitle($title)
                ->setMessage($message)
                ->setSendTypes(['apps'])
                ->send();
            $sent = true;
        } catch (\Throwable $e) {
            report($e);
        }

        // Keep failed sends pending so they can be retried safely later.
        if ($sent) {
            CoverageNotification::whereKey($subscriptions->modelKeys())
                ->update(['status' => 'notified']);
        }
    }
}
