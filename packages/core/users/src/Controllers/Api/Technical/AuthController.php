<?php

namespace Core\Users\Controllers\Api\Technical;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Core\Users\DataResources\ProviderResource;
use Core\Users\Models\Device;
use Core\Users\Requests\Api\EditProfileRequest;
use Core\Users\Requests\Api\ProviderLoginRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    private function getCredentials(Request $request)
    {
        $identifier = $request->identifier;
        $credentials = [];
        switch ($identifier) {
            case filter_var($identifier, FILTER_VALIDATE_EMAIL):
                $identifier = 'email';
                break;
            case is_numeric($identifier):
                $identifier = 'phone';
                break;
            default:
                $identifier = 'email';
                break;
        }
        $credentials[$identifier] = $request->identifier;
        if ($request->password) {
            $credentials['password'] = $request->password;
        }
        return $credentials;
    }

    public function login(ProviderLoginRequest $request)
    {
        if (!Auth::attempt($this->getCredentials($request))) {
            return $this->returnErrorMessage('Invalid phone number or password', [], [], 422);
        }
        $user = auth('api')->user();
        if (!$user->hasRole('technical'))
        return $this->returnErrorMessage(trans('you ara not technical'),[],[],422);
        if ($user->is_active == false)
        return $this->returnErrorMessage(trans('deactivation message'),[],[],422);
        if ($user->is_ban == true)
        return $this->returnErrorMessage(trans('banned message'),[],[],422);
        if ($request->type && $request->device_token) {
            Device::updateOrCreate([
                'user_id' => $user->id,
                'type' => $request->type,
            ],[
                'device_token' => $request->device_token
            ]);

        }
        // Generate token
        $user->token = $user->createToken('technical-login')->plainTextToken;
        return $this->returnData(trans('Login successfully'), ['data' => new ProviderResource($user)]);
    }

    // get profile
    public function profile()
    {
        $user = auth('api')->user();
        return (new ProviderResource($user))->additional(['status' => 'success', 'message' => '']);
    }

    // update profile
    public function edit_profile(EditProfileRequest $request)
    {
        \DB::beginTransaction();
        try {
            $user = auth('api')->user();
            $user->update(Arr::except($request->validated(), 'phone'));
            if ($request->phone && $request->phone != $user->phone) {
                $user->edit_profile()->updateOrCreate(['user_id' => $user->id], [
                    'fullname' => $request->fullname,
                    'phone'    => $request->phone,
                ]);
                $msg = "!رجاء بانتظار مافقه الاداره..تم التعديل بنجاح";
                //send notification with msg
            }
            if ($request->device_token) {
                Device::updateOrCreate([
                    'user_id' => $user->id,
                    'type' => 'ios',
                ],[
                    'device_token' => $request->device_token
                ]);
            }
            \DB::commit();
            return $this->returnData(trans('profile updated'), ['data' => new ProviderResource($user)]);
        } catch (\Exception $e) {
            \DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('can not updated'),[],[],422);
        }
    }
    public function logout(Request $request)
    {
        Device::where(['user_id' => $request->user()->id, 'device_token' => $request->device_token, 'type' => $request->type])->first()?->delete();
        $request->user()->tokens()->delete();
        $request->user()->update(['last_login_at' => null]);
        return $this->returnSuccessMessage(trans('user logged out'));
    }
}
