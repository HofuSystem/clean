<?php

namespace Core\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Users\Models\BranchEventLog;
use Core\Users\Models\UserAttribution;
use Illuminate\Http\Request;

class BranchWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Verify Token (You should set this in your .env / config)
        $branchToken = env('BRANCH_WEBHOOK_TOKEN');
        if ($branchToken && $request->header('X-Branch-Webhook-Token') !== $branchToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        
        // 2. Extract Data
        $eventName = $payload['name'] ?? null;
        $eventData = $payload['event_data'] ?? [];
        $userData = $payload['user_data'] ?? [];
        $lastAttributed = $payload['last_attributed_touch_data'] ?? [];

        if (!$eventName) {
            return response()->json(['status' => 'ignored', 'message' => 'No event name'], 200);
        }

        $userId = $userData['developer_identity'] ?? null;
        $eventId = $eventData['transaction_id'] ?? $eventData['event_id'] ?? null; // using transaction_id or event_id as unique identifier if possible
        
        // Sometimes branch uses id from metadata or just the timestamp, we can use a hash if not present
        if (!$eventId) {
            $eventId = md5(json_encode([$eventName, $userId, $payload['timestamp'] ?? time()]));
        }

        // Avoid duplication
        if (BranchEventLog::where('event_id', $eventId)->exists()) {
            return response()->json(['status' => 'ignored', 'message' => 'Duplicate event'], 200);
        }

        // 3. Log the Event
        BranchEventLog::create([
            'event_id' => $eventId,
            'user_id' => $userId,
            'event_name' => $eventName,
            'event_at' => isset($payload['timestamp']) ? date('Y-m-d H:i:s', $payload['timestamp'] / 1000) : now(),
            'is_attributed' => !empty($lastAttributed),
            'campaign_id' => $lastAttributed['~campaign_id'] ?? null,
            'ad_set_id' => $lastAttributed['~ad_set_id'] ?? null,
            'ad_id' => $lastAttributed['~ad_id'] ?? null,
            'transaction_id' => $eventData['transaction_id'] ?? null,
            'revenue' => $eventData['revenue'] ?? null,
            'currency' => $eventData['currency'] ?? null,
            'coupon_code' => $eventData['coupon'] ?? null,
            'raw_payload' => $payload,
        ]);

        // 4. Optionally Update Attribution Table if more complete data arrives
        if ($userId && !empty($lastAttributed)) {
            $attribution = UserAttribution::where('user_id', $userId)->first();
            if ($attribution && !$attribution->is_attributed) {
                $attribution->update([
                    'is_attributed' => true,
                    'ad_partner' => $lastAttributed['~channel'] ?? null, // Branch sometimes sends ad partner in channel or vice versa
                    'channel' => $lastAttributed['~channel'] ?? null,
                    'campaign_name' => $lastAttributed['~campaign'] ?? null,
                    'campaign_id' => $lastAttributed['~campaign_id'] ?? null,
                    'ad_set_name' => $lastAttributed['~ad_set_name'] ?? null,
                    'ad_set_id' => $lastAttributed['~ad_set_id'] ?? null,
                    'ad_name' => $lastAttributed['~ad_name'] ?? null,
                    'ad_id' => $lastAttributed['~ad_id'] ?? null,
                ]);
            }
        }

        // 5. Must return 200 OK fast
        return response()->json(['status' => 'success'], 200);
    }
}
