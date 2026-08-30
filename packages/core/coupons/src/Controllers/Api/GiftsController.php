<?php

namespace Core\Coupons\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Coupons\Services\GiftsService;
use Core\Coupons\DataResources\GiftApiResource;
use Core\Coupons\Requests\AttacheGiftsRequest;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group 1. Client App
 * @subgroup Coupons & Gifts
 */
class GiftsController extends Controller
{
    use ApiResponse;

    public function __construct(protected GiftsService $giftsService)
    {
    }

    /**
     * Get the gift that matches the user the most
     */
    public function getMatchingGift(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->returnErrorMessage(trans('User not authenticated'), [], [], 401);
        }

        $gift = $this->giftsService->getMatchingGift($user, $request->order_type);

        if (!$gift) {
            return $this->returnData(trans('No matching gift found'), ['data' => null]);
        }

        return $this->returnData(trans('Gift found'), [
            'data' => new GiftApiResource($gift)
        ]);
    }

    /**
     * Attach a specific gift to the user
     */
    public function attachGift(AttacheGiftsRequest $request)
    {
        try {
            $gift = $this->giftsService->attachToUser($request->gift_id, $request->user()->id);
            return $this->returnData(trans('Gift attached successfully'), [
                'data' => new GiftApiResource($gift)
            ]);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->returnErrorMessage(trans('Gift not found'), [], [], 404);
        }
        catch (\Exception $e) {
            $statusCode = is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600 ? $e->getCode() : 400;
            return $this->returnErrorMessage($e->getMessage(), [], [], $statusCode);
        }
    }

    /**
     * List user's gifts that are available and still match him
     */
    public function myGifts(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->returnErrorMessage(trans('User not authenticated'), [], [], 401);
        }

        $gift = $this->giftsService->getMyMatchingGifts($user, $request->order_type);

        if (!$gift) {
            return $this->returnData(trans('No matching gift found'), ['data' => null]);
        }

        return $this->returnData(trans('Gift retrieved successfully'), [
            'data' => new GiftApiResource($gift)
        ]);
    }
}