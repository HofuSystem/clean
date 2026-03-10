<?php

namespace Core\Coupons\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Coupons\Models\Coupon;
use Core\Coupons\Requests\FindCouponsRequest;
use Core\Settings\Traits\ApiResponse;
use Core\Coupons\Services\CouponsService;
use Core\Orders\Helpers\OrderHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CouponsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CouponsService $couponsService) {}

    public function get(FindCouponsRequest $request) {
        $userId = Auth::user()->id;
        $code = $request->code;
        $orderType = OrderHelper::getOrderType($request->order_type);
        $productsIds = $request->products_ids ?? [];
        $orderTotal = $request->order_total;

        // First, find the coupon by code and applying type
        $coupon = Coupon::where('code', $code)
            ->where('applying', 'manual')
            ->first();

        if (!$coupon) {
            return $this->returnErrorMessage(trans('Coupon code not found'));
        }

        // Check if coupon is active
        if ($coupon->status !== 'active') {
            return $this->returnErrorMessage(trans('This coupon is not active'));
        }

        // Check if coupon has started
        if ($coupon->start_at && Carbon::parse($coupon->start_at)->isFuture()) {
            return $this->returnErrorMessage(trans('This coupon has not started yet'));
        }

        // Check if coupon has expired
        if ($coupon->end_at && Carbon::parse($coupon->end_at)->isPast()) {
            return $this->returnErrorMessage(trans('This coupon has expired'));
        }

        // Check order type
        if ($orderType && $coupon->order_type && $coupon->order_type !== $orderType) {
            return $this->returnErrorMessage(trans('This coupon is not valid for this order type'));
        }

        // Check order minimum
        if ($coupon->order_minimum && $orderTotal < $coupon->order_minimum) {
            return $this->returnErrorMessage(trans('Order total must be at least :amount to use this coupon', ['amount' => $coupon->order_minimum]));
        }

        // Check order maximum
        if ($coupon->order_maximum && $orderTotal > $coupon->order_maximum) {
            return $this->returnErrorMessage(trans('Order total must not exceed :amount to use this coupon', ['amount' => $coupon->order_maximum]));
        }

        // Check max use per user
        if ($coupon->max_use_per_user) {
            $userUsageCount = $coupon->orders()
                ->where('client_id', $userId)
                ->count();
            
            if ($userUsageCount >= $coupon->max_use_per_user) {
                return $this->returnErrorMessage(trans('You have reached the maximum usage limit for this coupon'));
            }
        }

        // Check max use (total)
        if ($coupon->max_use) {
            $totalUsageCount = $coupon->orders()->count();
            
            if ($totalUsageCount >= $coupon->max_use) {
                return $this->returnErrorMessage(trans('This coupon has reached its maximum usage limit'));
            }
        }

        // Check products restrictions
        if (!empty($productsIds)) {
            $hasProducts = $coupon->products()->exists();
            $hasCategories = $coupon->categories()->exists();
            
            if ($hasProducts || $hasCategories) {
                $validProduct = false;
                
                // Check if any product is in coupon's products
                if ($hasProducts) {
                    $validProduct = $coupon->products()->whereIn('id', $productsIds)->exists();
                }
                
                // Check if any product is in coupon's categories
                if (!$validProduct && $hasCategories) {
                    $validProduct = $coupon->categories()
                        ->whereHas('products', function ($query) use ($productsIds) {
                            $query->whereIn('id', $productsIds);
                        })
                        ->orWhereHas('productsSub', function ($query) use ($productsIds) {
                            $query->whereIn('id', $productsIds);
                        })
                        ->exists();
                }
                
                if (!$validProduct) {
                    return $this->returnErrorMessage(trans('This coupon is not valid for the selected products'));
                }
            }
        }

        // Check user/role restrictions
        $hasUsers = $coupon->users()->exists();
        $hasRoles = $coupon->roles()->exists();
        
        if ($hasUsers || $hasRoles) {
            $hasAccess = false;
            
            // Check if user is in coupon's users
            if ($hasUsers) {
                $hasAccess = $coupon->users()->where('id', $userId)->exists();
            }
            
            // Check if user's role is in coupon's roles
            if (!$hasAccess && $hasRoles) {
                $hasAccess = $coupon->roles()
                    ->whereHas('users', function ($query) use ($userId) {
                        $query->where('id', $userId);
                    })
                    ->exists();
            }
            
            if (!$hasAccess) {
                return $this->returnErrorMessage(trans('This coupon is not available for your account'));
            }
        }

        // All validations passed - return only needed fields
        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'order_minimum' => $coupon->order_minimum,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'max_value' => $coupon->max_value,
        ];
        
        return $this->returnData(trans('Coupon found'), ['data' => $couponData]);
    }
}
