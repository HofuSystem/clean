<?php

namespace Core\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;
use Core\General\Helpers\Settings\PhoneNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Core\General\Helpers\ToolHelper;
use Core\Logs\Helpers\LogHelper;
use Core\Users\Helpers\UserHelper;
use Core\Users\Models\Device;
use Core\Users\Models\PasswordRestCode;
use Core\Users\Models\User;

class PasswordController extends Controller
{
    use ApiResponse;
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => array_merge(['required'], config("backend-settings.general.password_strength") ?? []),
        ],
        [
            "password.required" => trans('the password field is required')
        ]);
        if ($validator->fails()) {
            return $this->returnValidationError($validator);
        }

        User::where("id", ToolHelper::getUserId())->canResetPassword()
        ->update(['password' => bcrypt($request->input('password'))]);
        return $this->returnSuccessMessage(trans('password updated'));
    }
    public function forgetPassword(Request $request)
    {
        return UserHelper::getInstance()->forgetPassword($request);
    }

    // Edit Password
    public function editPassword(UpdatePasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth('api')->user();
            $user->update(array_only($request->validated(), ['password']));
            DB::commit();
            return (new UserProfileResource($user))->additional(['status' => 'success', 'message' => "تم التعديل بنجاح"]);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => 'لم يتم التعديل حاول مرة اخرى'], 401);
        }
    }

}
