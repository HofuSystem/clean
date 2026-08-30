<?php

namespace Core\Users\Controllers\Api;

use Core\Settings\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\EditProfileRequest;


/**
 * @group 1. Client App
 * @subgroup Auth
 */
class UserController extends Controller
{
    use ApiResponse;
    // Show Profile
    public function index()
    {
        $user = auth('api')->user();
        if (!$user->referral_code) {
            $user->update(['referral_code' => generate_unique_code(8, '\\App\\Models\\User', 'referral_code', 'alpha_numbers', 'lower')]);
        }
        return (new UserProfileResource($user))->additional(['status' => 'success', 'message' => '']);
    }


    // Edit Profile
    public function store(EditProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth('api')->user();

            $profile_data = ['country_id', 'city_id', 'is_infected'];


            $user->update(array_except($request->validated(), $profile_data));
            $user->profile()->updateOrCreate(['user_id' => $user->id], array_only($request->validated(), $profile_data));
            $msg = "تم التعديل بنجاح";


            if ($request->device_token) {
                $user->devices()->firstOrCreate($request->only(['device_token']) + ['type' => 'ios']);
            }
            DB::commit();
            return (new UserProfileResource($user))->additional(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'لم يتم التعديل حاول مرة اخرى'], 401);
        }
    }


    public function deleteAccount(Request $request)
    {
        $user = $request->user() ?? auth('api')->user();

        if (!$user) {
            return $this->returnErrorMessage(trans('User not found'), [], [], 401);
        }

        DB::beginTransaction();
        try {
            // Delete all tokens for API authentication
            $user->tokens()->delete();

            // Delete associated devices (FCM tokens)
            $user->devices()->delete();

            // Mark user inactive
            $user->update(['is_active' => false]);

            // Soft delete user record
            $user->delete();

            DB::commit();

            return $this->returnSuccessMessage(trans('Account deleted successfully'), 200);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('Failed to delete account'), [], [], 500);
        }
    }

    public function updateFcm(Request $request)
    {
        $user = auth('api')->user();
        if($request->device_token && $request->type){
            $user->devices()->updateOrCreate(['type' => $request->type],['device_token' => $request->device_token] );  
        };        
        return $this->returnData(trans('update was completed'),['status' => 'success', 'data' => null], 200);
    }
}
