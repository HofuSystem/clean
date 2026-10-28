<?php

namespace Core\Info\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Info\Services\CoverageNotificationService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group 4. General & Settings
 * @subgroup Geography
 */
class CoverageNotificationController extends Controller
{
    use ApiResponse;

    public function subscribe(Request $request)
    {
        // Empty/zero values are still accepted from older app versions.
        $request->merge([
            'city_id'     => $request->input('city_id') ?: null,
            'district_id' => $request->input('district_id') ?: null,
            'address_id'  => $request->input('address_id') ?: null,
        ]);

        $userId = auth('api')->id() ?? auth('sanctum')->id();
        if (!$userId) {
            return $this->returnErrorMessage(trans('User not authenticated'), [], [], 401);
        }

        $request->validate([
            'city_id'     => ['nullable', 'required_without_all:district_id,address_id', 'exists:cities,id'],
            'district_id' => ['nullable', 'required_without_all:city_id,address_id', 'exists:districts,id'],
            'address_id'  => [
                'nullable',
                Rule::exists('addresses', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'type'        => ['nullable', 'in:resume,expansion'],
        ]);

        $type       = $request->input('type', 'resume');
        $cityId     = $request->input('city_id');
        $districtId = $request->input('district_id');
        $addressId  = $request->input('address_id');

        if ($addressId) {
            $address = Address::where('user_id', $userId)->findOrFail($addressId);
            $cityId = $address->city_id;
            $districtId = $address->district_id;
        }

        CoverageNotificationService::subscribe($userId, $cityId, $districtId, $type, $addressId);

        return $this->returnSuccessMessage(trans('client.coverage_notification_subscribed'));
    }
}
