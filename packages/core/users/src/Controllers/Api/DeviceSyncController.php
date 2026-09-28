<?php

namespace Core\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\Device;
use Core\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeviceSyncController extends Controller
{
    use ApiResponse;

    /**
     * Idempotent device sync for clients, drivers, and technicians.
     * POST /api/devices/sync
     */
    public function sync(Request $request)
    {
        if (!config('notification.device_sync_enabled', true)) {
            return $this->returnErrorMessage(trans('Device sync is currently disabled'), [], [], 403);
        }

        // 1. Pre-validation normalization of aliases
        $input = $request->all();

        // Platform alias: fallback to 'type' if 'platform' is missing
        if (empty($input['platform']) && !empty($input['type']) && is_string($input['type'])) {
            $input['platform'] = strtolower(trim($input['type']));
        } elseif (!empty($input['platform']) && is_string($input['platform'])) {
            $input['platform'] = strtolower(trim($input['platform']));
        }

        // Permission alias: map 'granted' to canonical 'authorized'
        if (!empty($input['notification_permission']) && is_string($input['notification_permission'])) {
            $perm = strtolower(trim($input['notification_permission']));
            if ($perm === 'granted') {
                $perm = 'authorized';
            }
            $input['notification_permission'] = $perm;
        }

        // Context alias: map 'technician' to canonical 'technical'
        if (!empty($input['app_context']) && is_string($input['app_context'])) {
            $ctx = strtolower(trim($input['app_context']));
            if ($ctx === 'technician') {
                $ctx = 'technical';
            }
            $input['app_context'] = $ctx;
        }

        $request->merge($input);

        // 2. Validation with standard enum constraints
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'platform' => 'required|string|in:ios,android,huawei',
            'installation_id' => 'nullable|string|max:255',
            'app_context' => 'nullable|string|in:client,driver,technical,unknown',
            'notification_permission' => 'nullable|string|in:authorized,denied,not_determined,provisional,unknown',
            'app_version' => 'nullable|string|max:50',
            'os_version' => 'nullable|string|max:50',
            'device_model' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->returnErrorMessage($validator->errors()->first(), $validator->errors(), [], 422);
        }

        try {
            $user = auth('api')->user() ?? auth('sanctum')->user() ?? auth()->user();
            if (!$user) {
                return $this->returnErrorMessage(trans('unauthenticated'), [], [], 401);
            }

            $device = DB::transaction(function () use ($request, $user) {
                $existing = null;

                // 1. Look up by installation_id first if provided
                if (!empty($request->installation_id)) {
                    $existing = Device::withTrashed()->where('installation_id', $request->installation_id)->first();
                }

                // 2. Look up by device_token if not found by installation_id
                if (!$existing && !empty($request->device_token)) {
                    $existing = Device::withTrashed()->where('device_token', $request->device_token)->first();
                }

                $appContext = $request->app_context ?: 'client';
                $permission = $request->notification_permission ?: 'unknown';

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update([
                        'user_id' => $user->id,
                        'installation_id' => $request->installation_id ?: $existing->installation_id,
                        'device_token' => $request->device_token,
                        'type' => $request->platform ?: $existing->type,
                        'app_context' => $appContext,
                        'token_status' => 'active',
                        'notification_permission' => $permission,
                        'permission_checked_at' => $request->notification_permission ? now() : $existing->permission_checked_at,
                        'token_refreshed_at' => now(),
                        'last_seen_at' => now(),
                        'app_version' => $request->app_version ?: $existing->app_version,
                        'os_version' => $request->os_version ?: $existing->os_version,
                        'device_model' => $request->device_model ?: $existing->device_model,
                        'invalidated_at' => null,
                        'invalid_reason' => null,
                    ]);

                    return $existing->fresh();
                }

                // 3. Create fresh record
                return Device::create([
                    'user_id' => $user->id,
                    'installation_id' => $request->installation_id,
                    'device_token' => $request->device_token,
                    'type' => $request->platform,
                    'app_context' => $appContext,
                    'token_status' => 'active',
                    'notification_permission' => $permission,
                    'permission_checked_at' => $request->notification_permission ? now() : null,
                    'token_refreshed_at' => now(),
                    'last_seen_at' => now(),
                    'app_version' => $request->app_version,
                    'os_version' => $request->os_version,
                    'device_model' => $request->device_model,
                ]);
            });

            return $this->returnData(trans('device synced successfully'), [
                'data' => [
                    'device_id' => $device->id,
                    'installation_id' => $device->installation_id,
                    'token_status' => $device->token_status,
                    'notification_permission' => $device->notification_permission,
                    'app_context' => $device->app_context,
                    'platform' => $device->type,
                ]
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 500);
        }
    }

    /**
     * Detach user from device upon logout.
     * DELETE /api/devices/{installation_id}/user
     */
    public function detachUser(Request $request, $installationId)
    {
        try {
            $user = auth('api')->user() ?? auth('sanctum')->user() ?? auth()->user();
            if (!$user) {
                return $this->returnErrorMessage(trans('unauthenticated'), [], [], 401);
            }

            $device = Device::where('installation_id', $installationId)
                ->where('user_id', $user->id)
                ->first();

            if ($device) {
                $device->update(['user_id' => null]);
            }

            return $this->returnSuccessMessage(trans('device detached successfully'));
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 500);
        }
    }

    /**
     * Explicit marketing notification opt-in/opt-out.
     * PUT /api/client/notification-preference
     */
    public function updateMarketingPreference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'marketing_push_enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->returnErrorMessage($validator->errors()->first(), $validator->errors(), [], 422);
        }

        try {
            $user = auth('api')->user() ?? auth('sanctum')->user() ?? auth()->user();
            if (!$user) {
                return $this->returnErrorMessage(trans('unauthenticated'), [], [], 401);
            }

            $enabled = (bool) $request->marketing_push_enabled;

            $user->update([
                'is_allow_notify' => $enabled ? 1 : 0,
                'is_allow_notify_confirmed_at' => now(),
            ]);

            return $this->returnData(trans('notification preference updated successfully'), [
                'data' => [
                    'is_allow_notify' => (int) $user->is_allow_notify,
                    'is_allow_notify_confirmed_at' => $user->is_allow_notify_confirmed_at?->toIso8601String(),
                ]
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 500);
        }
    }
}
