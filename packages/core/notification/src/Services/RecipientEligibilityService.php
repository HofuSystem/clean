<?php

namespace Core\Notification\Services;

use Core\Notification\Helpers\NotificationChannelResolver;

use Core\Notification\Helpers\NotificationDataNormalizer;
use Core\Notification\Models\Notification;
use Core\Users\Models\Device;
use Core\Users\Models\User;

class RecipientEligibilityService
{
    /**
     * Build the query for targeted users according to notification filters.
     */
    public function getTargetedUsersQuery(Notification $notification, array $select = [])
    {
        $for = $notification->for;
        $forData = $notification->for_data;
        $purpose = $notification->purpose;

        $query = User::query();

        if (!empty($select)) {
            $query->select($select);
        }

        // 1. Role & Status filter based on Purpose
        if ($purpose === 'marketing') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'client');
            })->where('is_active', 1);
        }
        // Note: For transactional and system notifications, no role filter is applied
        // so that drivers, technicians, and clients can all receive their operational pushes.

        // 2. Recipient filters (for / for_data)
        if ($for === 'users') {
            $userIds = NotificationDataNormalizer::toUserIds($forData);
            if (!empty($userIds)) {
                $query->whereIn('id', $userIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($for === 'email') {
            $emails = NotificationDataNormalizer::toStringList($forData);
            if (!empty($emails)) {
                $query->whereIn('email', $emails);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($for === 'phone') {
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
        }

        // 3. Date filters
        if (!empty($notification->register_from)) {
            $query->where('created_at', '>=', $notification->register_from);
        }
        if (!empty($notification->register_to)) {
            $query->where('created_at', '<=', $notification->register_to);
        }

        // 4. Order count and order date filters
        $ordersFrom = $notification->orders_from;
        $ordersTo = $notification->orders_to;
        $ordersMin = $notification->orders_min;
        $ordersMax = $notification->orders_max;

        if (isset($ordersFrom) || isset($ordersTo) || isset($ordersMin) || isset($ordersMax)) {
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

            if (isset($ordersMin)) {
                $query->having('filtered_orders_count', '>=', $ordersMin);
            }
            if (isset($ordersMax)) {
                $query->having('filtered_orders_count', '<=', $ordersMax);
            }
        }

        return $query;
    }

    /**
     * Evaluate eligibility for all targeted users and return actionable results.
     * Supports Observe vs Enforce modes via configuration.
     */
    public function evaluateEligibility(Notification $notification): array
    {
        $targetedQuery = $this->getTargetedUsersQuery($notification, [
            'id', 'fullname', 'phone', 'email', 'is_active', 'is_allow_notify', 'is_allow_notify_confirmed_at'
        ]);

        $permissionMode = config('notification.permission_mode', 'observe'); // 'observe' | 'enforce'
        $marketingMode = config('notification.marketing_preference_mode', 'observe'); // 'observe' | 'enforce'

        $targetedUserIds = [];
        $userEligibility = [];
        $eligibleDevices = [];
        $seenTokens = [];
        $eligibleUsersCount = 0;

        $channel = NotificationChannelResolver::resolveChannel($notification);
        $resolvedPurpose = NotificationChannelResolver::resolvePurpose($notification);

        // Large campaigns must never hydrate the entire audience and all of its
        // devices at once. Keeping the working set bounded prevents queue workers
        // with a 128 MB memory limit from dying before dispatch begins.
        $targetedQuery->orderBy('id')->chunkById(500, function ($users) use (
            $notification,
            $permissionMode,
            $marketingMode,
            $channel,
            $resolvedPurpose,
            &$targetedUserIds,
            &$userEligibility,
            &$eligibleDevices,
            &$seenTokens,
            &$eligibleUsersCount
        ): void {
            $userIds = $users->pluck('id')->all();
            array_push($targetedUserIds, ...$userIds);

            $devicesByUser = Device::query()
                ->whereIn('user_id', $userIds)
                ->whereNotNull('device_token')
                ->where('device_token', '!=', '')
                ->where('token_status', '!=', 'invalid')
                ->get([
                    'id', 'user_id', 'device_token', 'type', 'installation_id',
                    'app_context', 'notification_permission',
                ])
                ->groupBy('user_id');

            foreach ($users as $user) {
                $userDevices = $devicesByUser->get($user->id, collect());
                $isMarketingBlocked = $channel === NotificationChannelResolver::CHANNEL_APP_FCM
                    && $resolvedPurpose === NotificationChannelResolver::PURPOSE_MARKETING
                    && $user->is_allow_notify == 0
                    && !is_null($user->is_allow_notify_confirmed_at);

                if ($isMarketingBlocked) {
                    $userEligibility[$user->id] = [
                        'status' => 'marketing_disabled',
                        'reason' => $marketingMode === 'enforce'
                            ? 'User opted out of marketing pushes'
                            : 'User opted out of marketing pushes (Observed - not enforced)',
                    ];

                    if ($marketingMode === 'enforce') {
                        continue;
                    }
                }

                if ($userDevices->isEmpty()) {
                    $userEligibility[$user->id] = [
                        'status' => 'no_device',
                        'reason' => 'No registered device token found',
                    ];
                    continue;
                }

                $allowedDevices = $userDevices->filter(
                    fn ($device) => $device->notification_permission !== 'denied'
                );

                if ($allowedDevices->isEmpty()) {
                    $userEligibility[$user->id] = [
                        'status' => 'permission_denied',
                        'reason' => $permissionMode === 'enforce'
                            ? 'Push notifications denied on all user devices'
                            : 'Push notifications denied on all user devices (Observed - not enforced)',
                    ];

                    if ($permissionMode === 'enforce') {
                        continue;
                    }

                    $targetDevices = $userDevices;
                } else {
                    $targetDevices = $permissionMode === 'enforce' ? $allowedDevices : $userDevices;
                    if (!isset($userEligibility[$user->id])) {
                        $userEligibility[$user->id] = ['status' => 'eligible', 'reason' => null];
                    }
                }

                $eligibleUsersCount++;

                foreach ($targetDevices as $device) {
                    if (!isset($seenTokens[$device->device_token])) {
                        $seenTokens[$device->device_token] = true;
                        $eligibleDevices[] = [
                            'device_id' => $device->id,
                            'user_id' => $user->id,
                            'token' => $device->device_token,
                            'platform' => $device->type,
                            'installation_id' => $device->installation_id,
                            'app_context' => $device->app_context,
                        ];
                    }
                }
            }
        }, 'id');

        return [
            'targeted_user_ids' => $targetedUserIds,
            'targeted_users_count' => count($targetedUserIds),
            'eligible_users_count' => $eligibleUsersCount,
            'eligible_devices_count' => count($eligibleDevices),
            'user_eligibility' => $userEligibility,
            'eligible_devices' => $eligibleDevices,
        ];
    }

    /**
     * Generate a read-only audience preview without persisting any data,
     * dispatching jobs, or calling FCM/Telegram.
     * Evaluates users in small chunks to avoid memory spikes.
     *
     * @param array $params
     * @return array
     */
    public function previewAudience(array $params): array
    {
        $notification = new Notification($params);
        $purpose = $params['purpose'] ?? ($notification->purpose ?: 'marketing');
        $notification->purpose = $purpose;

        $targetedQuery = $this->getTargetedUsersQuery($notification, [
            'id', 'is_active', 'is_allow_notify', 'is_allow_notify_confirmed_at'
        ]);

        $totalMatching = (clone $targetedQuery)->count();
        if ($totalMatching === 0) {
            return [
                'total_matching_users' => 0,
                'active_users' => 0,
                'inactive_users' => 0,
                'eligible_users' => 0,
                'eligible_devices' => 0,
                'breakdown' => [
                    'no_device' => 0,
                    'no_valid_token' => 0,
                    'marketing_disabled' => 0,
                    'permission_denied' => 0,
                    'legacy_unknown' => 0,
                    'inactive' => 0,
                ],
                'platforms' => ['ios' => 0, 'android' => 0, 'huawei' => 0, 'unknown' => 0],
                'device_ratio' => 0,
                'warning' => 'لا يوجد مستخدمون مطابقون لمعايير الجمهور المحددة.',
            ];
        }

        $activeUsers = (clone $targetedQuery)->where('is_active', 1)->count();
        $inactiveUsers = max(0, $totalMatching - $activeUsers);

        $permissionMode = config('notification.permission_mode', 'observe');
        $marketingMode = config('notification.marketing_preference_mode', 'observe');

        $eligibleUsersCount = 0;
        $eligibleDevicesCount = 0;
        $noDeviceCount = 0;
        $noValidTokenCount = 0;
        $marketingDisabledCount = 0;
        $permissionDeniedCount = 0;
        $legacyUnknownCount = 0;
        $platformDist = ['ios' => 0, 'android' => 0, 'huawei' => 0, 'unknown' => 0];
        $seenTokens = [];

        // Chunk through targeted users (500 at a time) to avoid loading all models into memory
        $targetedQuery->orderBy('id')->chunk(500, function ($users) use (
            $notification, $purpose, $permissionMode, $marketingMode,
            &$eligibleUsersCount, &$eligibleDevicesCount,
            &$noDeviceCount, &$noValidTokenCount, &$marketingDisabledCount,
            &$permissionDeniedCount, &$legacyUnknownCount,
            &$platformDist, &$seenTokens
        ) {
            $userIds = $users->pluck('id')->toArray();
            $devices = Device::whereIn('user_id', $userIds)
                ->select(['id', 'user_id', 'device_token', 'type', 'notification_permission', 'token_status'])
                ->get();
            $devicesByUser = $devices->groupBy('user_id');

            foreach ($users as $user) {
                if (is_null($user->is_allow_notify_confirmed_at)) {
                    $legacyUnknownCount++;
                }

                $userDevices = $devicesByUser->get($user->id, collect());
                $validTokens = $userDevices->filter(fn($d) => !empty($d->device_token) && $d->token_status !== 'invalid');

                // Check marketing opt-out
                $channel = NotificationChannelResolver::resolveChannel($notification);
            $resolvedPurpose = NotificationChannelResolver::resolvePurpose($notification);
            $isMarketingBlocked = ($channel === NotificationChannelResolver::CHANNEL_APP_FCM && $resolvedPurpose === NotificationChannelResolver::PURPOSE_MARKETING && $user->is_allow_notify == 0 && !is_null($user->is_allow_notify_confirmed_at));
                if ($isMarketingBlocked) {
                    $marketingDisabledCount++;
                    if ($marketingMode === 'enforce') {
                        continue;
                    }
                }

                // Check device existence
                if ($userDevices->isEmpty()) {
                    $noDeviceCount++;
                    continue;
                }

                // Check valid token existence
                if ($validTokens->isEmpty()) {
                    $noValidTokenCount++;
                    continue;
                }

                // Check notification permission
                $deniedDevices = $validTokens->filter(fn($dev) => $dev->notification_permission === 'denied');
                $allowedDevices = $validTokens->filter(fn($dev) => $dev->notification_permission !== 'denied');

                if ($allowedDevices->isEmpty()) {
                    $permissionDeniedCount++;
                    if ($permissionMode === 'enforce') {
                        continue;
                    } else {
                        $targetDevices = $validTokens;
                    }
                } else {
                    $targetDevices = ($permissionMode === 'enforce') ? $allowedDevices : $validTokens;
                }

                $eligibleUsersCount++;

                foreach ($targetDevices as $dev) {
                    $token = $dev->device_token;
                    if (!isset($seenTokens[$token])) {
                        $seenTokens[$token] = true;
                        $eligibleDevicesCount++;

                        $plat = strtolower($dev->type ?? 'unknown');
                        if (!in_array($plat, ['ios', 'android', 'huawei'])) {
                            $plat = 'unknown';
                        }
                        $platformDist[$plat] = ($platformDist[$plat] ?? 0) + 1;
                    }
                }
            }
        });

        $ratio = $totalMatching > 0 ? ($eligibleDevicesCount / $totalMatching) : 0;
        $deviceRatioPercent = round($ratio * 100, 1);
        $warning = null;
        if ($totalMatching > 0 && $eligibleDevicesCount === 0) {
            $warning = 'تحذير: لا توجد أجهزة مؤهلة لاستلام الإشعار وفق الشروط المحددة.';
        } elseif ($totalMatching > 0 && $deviceRatioPercent < 25.0) {
            $warning = "تحذير: نسبة الأجهزة المؤهلة منخفضة ({$deviceRatioPercent}% من الجمهور المطابق). يرجى مراجعة معايير الاستهداف.";
        }

        return [
            'total_matching_users' => $totalMatching,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'eligible_users' => $eligibleUsersCount,
            'eligible_devices' => $eligibleDevicesCount,
            'breakdown' => [
                'no_device' => $noDeviceCount,
                'no_valid_token' => $noValidTokenCount,
                'marketing_disabled' => $marketingDisabledCount,
                'permission_denied' => $permissionDeniedCount,
                'legacy_unknown' => $legacyUnknownCount,
                'inactive' => $inactiveUsers,
            ],
            'platforms' => $platformDist,
            'device_ratio' => $deviceRatioPercent,
            'warning' => $warning,
        ];
    }
}
