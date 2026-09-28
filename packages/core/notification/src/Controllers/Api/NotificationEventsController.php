<?php

namespace Core\Notification\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationEventsController extends Controller
{
    use ApiResponse;

    /**
     * Record push notification interaction events (received, opened)
     * POST /api/client/notifications/events
     */
    public function recordEvent(Request $request)
    {
        // 1. Feature Flag check
        if (!config('notification.delivery_events_enabled', false)) {
            return $this->returnErrorMessage(trans('Events recording is currently disabled'), [], [], 403);
        }

        // 2. Authentication check
        $user = auth('api')->user() ?? auth('sanctum')->user() ?? auth()->user();
        if (!$user) {
            return $this->returnErrorMessage(trans('unauthenticated'), [], [], 401);
        }

        // 3. Validation
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer',
            'event_type' => 'required|string|in:received,opened',
            'installation_id' => 'nullable|string|max:255',
            'device_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->returnErrorMessage($validator->errors()->first(), $validator->errors(), [], 422);
        }

        if (empty($request->installation_id) && empty($request->device_token)) {
            return $this->returnErrorMessage(trans('installation_id or device_token is required'), [], [], 422);
        }

        try {
            // 4. Notification lookup
            $notification = Notification::find($request->notification_id);
            if (!$notification) {
                return $this->returnErrorMessage(trans('Notification not found'), [], [], 404);
            }

            // 5. Device lookup & strict ownership verification
            $device = Device::query()
                ->when($request->installation_id, function ($q) use ($request) {
                    $q->where('installation_id', $request->installation_id);
                })
                ->when(!$request->installation_id && $request->device_token, function ($q) use ($request) {
                    $q->where('device_token', $request->device_token);
                })
                ->first();

            if (!$device) {
                return $this->returnErrorMessage(trans('Device not found'), [], [], 404);
            }

            if ($device->user_id != $user->id) {
                return $this->returnErrorMessage(trans('Access denied'), [], [], 403);
            }

            // 6. NotificationToken attempt verification
            $tokenRecord = NotificationToken::where('notification_id', $notification->id)
                ->where('user_id', $user->id)
                ->where(function ($q) use ($device) {
                    $hasCondition = false;
                    if (!empty($device->installation_id)) {
                        $q->where('installation_id', $device->installation_id);
                        $hasCondition = true;
                    }
                    if (!empty($device->device_token)) {
                        if ($hasCondition) {
                            $q->orWhere('token', $device->device_token);
                        } else {
                            $q->where('token', $device->device_token);
                            $hasCondition = true;
                        }
                    }
                    if ($device->id) {
                        $q->orWhere('device_id', $device->id);
                    }
                })
                ->first();

            if (!$tokenRecord) {
                return $this->returnErrorMessage(trans('No delivery record found for this device and notification'), [], [], 404);
            }

            $eventType = $request->event_type;

            DB::transaction(function () use ($device, $notification, $tokenRecord, $eventType) {
                $now = now();

                // 7. Idempotent recording of received / opened
                if ($eventType === 'received') {
                    if (is_null($tokenRecord->received_at)) {
                        $tokenRecord->update([
                            'received_at' => $now,
                            'status' => in_array($tokenRecord->status, ['opened']) ? $tokenRecord->status : 'received',
                        ]);
                        $notification->increment('received_count');
                    }
                    $device->update(['last_notification_received_at' => $now, 'last_seen_at' => $now]);
                } elseif ($eventType === 'opened') {
                    if (is_null($tokenRecord->opened_at)) {
                        $tokenRecord->update([
                            'opened_at' => $now,
                            'status' => 'opened',
                        ]);
                        $notification->increment('opened_count');
                    }
                    $device->update(['last_notification_opened_at' => $now, 'last_seen_at' => $now]);
                }
            });

            return $this->returnSuccessMessage(trans('event recorded successfully'));
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 500);
        }
    }
}
