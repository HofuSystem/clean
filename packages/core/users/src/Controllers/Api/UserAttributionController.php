<?php

namespace Core\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Users\Models\UserAttribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAttributionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'install_uuid' => 'required|uuid',
            'branch_device_token' => 'nullable|string',
            'platform' => 'nullable|string',
            'app_version' => 'nullable|string',
            'os_version' => 'nullable|string',
            'device_model' => 'nullable|string',
            'is_attributed' => 'nullable|boolean',
            'ad_partner' => 'nullable|string',
            'channel' => 'nullable|string',
            'campaign_name' => 'nullable|string',
            'campaign_id' => 'nullable|string',
            'ad_set_name' => 'nullable|string',
            'ad_set_id' => 'nullable|string',
            'ad_name' => 'nullable|string',
            'ad_id' => 'nullable|string',
            'referring_link' => 'nullable|string',
            'branch_raw_data' => 'nullable|array',
        ]);

        $userId = Auth::id();
        
        $attribution = UserAttribution::where('user_id', $userId)
            ->where('install_uuid', $request->install_uuid)
            ->first();

        if (!$attribution) {
            $attribution = new UserAttribution([
                'user_id' => $userId,
                'install_uuid' => $request->install_uuid,
                'first_seen_at' => now(),
            ]);
        }

        // Only update if not previously attributed, or just fill missing data
        // For first acquisition, we shouldn't overwrite the first source if it's already attributed
        if (!$attribution->is_attributed && $request->is_attributed) {
            $attribution->is_attributed = $request->is_attributed;
            $attribution->ad_partner = $request->ad_partner;
            $attribution->channel = $request->channel;
            $attribution->campaign_name = $request->campaign_name;
            $attribution->campaign_id = $request->campaign_id;
            $attribution->ad_set_name = $request->ad_set_name;
            $attribution->ad_set_id = $request->ad_set_id;
            $attribution->ad_name = $request->ad_name;
            $attribution->ad_id = $request->ad_id;
            $attribution->referring_link = $request->referring_link;
        }

        // Update technical fields always
        $attribution->branch_device_token = $request->branch_device_token ?? $attribution->branch_device_token;
        $attribution->platform = $request->platform ?? $attribution->platform;
        $attribution->app_version = $request->app_version ?? $attribution->app_version;
        $attribution->os_version = $request->os_version ?? $attribution->os_version;
        $attribution->device_model = $request->device_model ?? $attribution->device_model;
        
        if ($request->has('branch_raw_data')) {
            $attribution->branch_raw_data = $request->branch_raw_data;
        }

        $attribution->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Attribution saved successfully',
            'data' => $attribution
        ]);
    }
}
