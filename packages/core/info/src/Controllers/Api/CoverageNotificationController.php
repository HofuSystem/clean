<?php

namespace Core\Info\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Info\Services\CoverageNotificationService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;

class CoverageNotificationController extends Controller
{
    use ApiResponse;

    public function subscribe(Request $request)
    {
        $request->validate([
            'city_id'     => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'type'        => ['nullable', 'in:resume,expansion'],
        ]);

        $userId = auth('api')->id() ?? auth('sanctum')->id();
        if (!$userId) {
            return $this->returnErrorMessage(trans('User not authenticated'), [], [], 401);
        }

        $type       = $request->input('type', 'resume');
        $cityId     = $request->input('city_id');
        $districtId = $request->input('district_id');

        CoverageNotificationService::subscribe($userId, $cityId, $districtId, $type);

        return $this->returnSuccessMessage(trans('client.coverage_notification_subscribed'));
    }
}
