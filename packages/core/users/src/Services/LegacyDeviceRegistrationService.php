<?php

namespace Core\Users\Services;

use Core\Users\Models\Device;
use Core\Users\Models\User;

class LegacyDeviceRegistrationService
{
    /** Keep older login/profile APIs usable while refreshing their device state. */
    public function register(User $user, ?string $platform, string $token, string $appContext): ?Device
    {
        if (!in_array($platform, ['ios', 'android', 'huawei'], true) || $token === '') {
            return null;
        }

        $device = Device::firstOrNew(['user_id' => $user->id, 'type' => $platform]);
        $tokenChanged = $device->exists && $device->device_token !== $token;
        $legacyInvalidation = $device->token_status === 'invalid'
            && $device->invalid_reason === 'Legacy topic subscription failure';

        $device->device_token = $token;
        $device->app_context = $appContext;
        $device->last_seen_at = now();

        // A legacy topic-subscription error does not prove the token is invalid
        // for direct FCM. A genuinely new token also deserves a fresh attempt.
        if (!$device->exists || $tokenChanged || $legacyInvalidation) {
            $device->token_status = 'active';
            $device->token_refreshed_at = now();
            $device->invalidated_at = null;
            $device->invalid_reason = null;
        }

        $device->save();

        return $device;
    }
}
