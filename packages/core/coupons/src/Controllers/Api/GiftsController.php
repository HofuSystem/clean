<?php

namespace Core\Coupons\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Coupons\Services\GiftsService;
use Core\Coupons\DataResources\GiftApiResource;

use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftsController extends Controller
{
    use ApiResponse;

    public function __construct(protected GiftsService $giftsService) {}

    /**
     * Get the gift that matches the user the most
     */
    public function getMatchingGift(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->returnErrorMessage(trans('User not authenticated'), [], [], 401);
        }

        $gift = $this->giftsService->getMatchingGift($user,$request->order_type);

        if (!$gift) {
            return $this->returnData(trans('No matching gift found'), ['data' => null]);
        }

        return $this->returnData(trans('Gift found'), [
            'data' => new GiftApiResource($gift)
        ]);

    }
}
