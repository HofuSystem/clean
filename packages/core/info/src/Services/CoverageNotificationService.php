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
    public static function subscribe(int $userId, ?int $cityId, ?int $districtId, string $type = 'resume')
    {
        return CoverageNotification::updateOrCreate([
            'user_id'     => $userId,
            'city_id'     => $cityId,
            'district_id' => $districtId,
        ], [
            'type'   => $type,
            'status' => 'pending',
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
            $query->where('city_id', $cityId);
        } else {
            return;
        }

        $subscriptions = $query->with('user')->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $userIds = $subscriptions->pluck('user_id')->unique()->filter()->toArray();
        $users = User::whereIn('id', $userIds)->get();

        if ($users->isNotEmpty()) {
            $title   = trans('Bushing! Service is now active in your area!');
            $message = trans('Great news! Service is now active in your location. We are excited to serve you!');

            try {
                NotificationsManger::getInstance()
                    ->setUsers($users)
                    ->setTitle($title)
                    ->setMessage($message)
                    ->setSendTypes(['apps'])
                    ->send();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Mark subscriptions as notified
        foreach ($subscriptions as $subscription) {
            $subscription->update(['status' => 'notified']);
        }
    }
}
