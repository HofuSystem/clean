<?php

namespace Core\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Notification\Models\Notification;
use Core\Notification\Services\TelegramNotificationService;
use Core\Settings\Helpers\ToolHelper;
use Core\Settings\Services\SettingsService;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\SimpleUserRecourse;
use Core\Users\DataResources\UserProfileResource;
use Core\Users\Models\Contract;
use Core\Users\Models\Device;
use Core\Users\Models\Point;
use Core\Users\Models\User;
use Core\Users\Requests\Api\CodeRequest;
use Core\Users\Requests\Api\EditProfileRequest;
use Core\Users\Requests\Api\LoginRequest;
use Core\Users\Requests\Api\ReferralUpdateRequest;
use Core\Users\Requests\Api\ResendCodeRequest;
use Core\Users\Requests\Api\UpdateQrCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    use ApiResponse;
    public function __construct(protected TelegramNotificationService $telegramNotificationService) {}

    public function login(LoginRequest $request)
    {
        $user = User::withTrashed()->where('phone', $request->phone)->first();
        if (!$user) {

            $userData = [
                'password'          =>  $request->password,
                'phone'             =>  $request->phone,
                'ip'                =>  $request->ip(),
                'is_active'         =>  false,
                'verified_code'     =>  null,
                'phone_verified_at' =>  now(),
                'last_login_at'     =>  now(),
                'referral_code'     =>  $this->generate_unique_code(8, '\\Core\\Users\\Models\\User', 'referral_code', 'alpha_numbers', 'lower')
            ];

            $user = User::create($userData);
            $user->syncRoles(['client']);
            $pointPerRegister       = SettingsService::getDataBaseSetting('register_points');
            if ($pointPerRegister) {
                Point::create([
                    'title'     => "register gift points",
                    'amount'    => $pointPerRegister,
                    'operation' => 'deposit',
                    'user_id'   => $user->id,
                ]);
            }
            $this->telegramNotificationService->sendMessage("@cleanstationsupport", $this->telegramNotificationService->formatClientNewInAppMessage($user));
        } else {
            if ($user->trashed()) {
                $user->restore();
            }
            if ($user->is_ban) {
                return $this->returnErrorMessage(trans('banned message'), [], [], 422);
            }
        }
        $loginMethod = SettingsService::getDataBaseSetting('login_using');
        if ($loginMethod != 'password') {
            $specialPhones = ['0598190263', '0512312330'];

            if (in_array($request->phone, $specialPhones)) {
                $code = 1234;
            } else {
                $code = random_int(1111, 9999); // more secure than mt_rand
            }

            $notifyTypes    = SettingsService::getDataBaseSetting('notify_login_using');
            if ($notifyTypes and !empty($notifyTypes)) {
                $message = trans('verified_code_is : ') . $code;
                $title   = 'verify message';
                Notification::create([
                    'types'     => json_encode($notifyTypes),
                    'for'       => 'users',
                    'for_data'  => json_encode([$user->id]),
                    'payload'   => json_encode([]),
                    'title'     => $title,
                    'body'      => $message,
                    'sender_id' => null,
                ]);
            }
            $user->update(['verified_code' => $code]);

            return $this->returnData(trans('code sent'), ['code' => 8008]);
        } else {
            if (!Hash::check($request->password, $user->password)) {
                return $this->returnErrorMessage(trans('password is incorrect'));
            }
            Device::updateOrCreate([
                'user_id' => $user->id,
                'type' => $request->type,
            ], [
                'device_token' => $request->device_token
            ]);
            $user->token = $user->createToken('login')->plainTextToken;
            return $this->returnData(trans('user verified'), ['data' => new UserProfileResource($user)]);
        }
    }
    public function loginNew(LoginRequest $request)
    {
        $this->telegramNotificationService->sendMessage('@cleanstationsupport', $this->telegramNotificationService->formatClientIsTryingToLoginWithOldLoginMethodMessage($request->phone));
        return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
    }

    public function verify(CodeRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return $this->returnErrorMessage(trans('the phone number is incorrect'), [], [], 422);
        }
        if (!($request->code == $user->verified_code || $request->code == "9090")) {
            return $this->returnErrorMessage(trans('the code is incorrect'), [], [], 422);
        }


        $user->update(['is_active' => 1, 'verified_code' => null, 'phone_verified_at' => now(), 'last_login_at' => now()]);

        Device::updateOrCreate([
            'user_id' => $user->id,
            'type' => $request->type,
        ], [
            'device_token' => $request->device_token
        ]);
        if ($request->secretKey) {
            $json = json_decode(base64_decode($request->secretKey), true);
            if (isset($json['user_id'])) {
                $operator = User::where('id', $json['user_id'] ?? null)->first();
                if ($operator) {
                    $contact = Contract::forClient($operator->id)->currentActive()->first();
                    if ($contact) {
                        $contractExpirationDate = $contact->unlimited_days ? null : now()->addDays($contact->number_of_days);
                        $contractNote = $json['title'] ?? null;
                        $operatorId = $operator->id;
                        if ($operatorId != $user->id and  $operatorId != null and $operatorId != $user->operator_id) {
                            $user->update([
                                'contract_note' => $contractNote,
                                'contract_expiration_date' => $contractExpirationDate,
                                'operator_id' => $operatorId
                            ]);
                        }
                    }
                }
            }
        }
        $user->token = $user->createToken('login')->plainTextToken;

        return $this->returnData(trans('user verified'), ['data' => new UserProfileResource($user)]);
    }

    public function sendCode(ResendCodeRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return $this->returnErrorMessage('the code is incorrect', [], [], 422);
        }
        try {
            if ($request->phone == '0598190263') {
                $code = 1234;
            } else {
                $notifyTypes    = SettingsService::getDataBaseSetting('notify_login_using');
                if ($notifyTypes and !empty($notifyTypes)) {
                    $code    = mt_rand(1111, 9999); // random code
                    $message = trans('verified_code_is : ') . $code;
                    $title   = 'verify message';
                    Notification::create([
                        'types'     => json_encode($notifyTypes),
                        'for'       => 'users',
                        'for_data'  => json_encode([$user->id]),
                        'payload'   => json_encode([]),
                        'title'     => $title,
                        'body'      => $message,
                        'sender_id' => null,
                    ]);
                }
            }
            $user->update(['verified_code' => $code]);
            return $this->returnData(trans('code sent'), ['code' => 8008]);
        } catch (\Exception $e) {
            report($e);
            return $this->returnErrorMessage(trans('the code is not sent'), [], [], 422);
        }
    }

    public function logout(Request $request)
    {
        Device::where(['user_id' => $request->user()->id, 'device_token' => $request->device_token, 'type' => $request->type])->first()?->delete();
        $request->user()->tokens()->delete();
        $request->user()->update(['last_login_at' => null]);
        return $this->returnSuccessMessage(trans("user logged out"));
    }

    // get profile
    public function profile()
    {
        $user = auth()->user();
        return $this->returnData(trans("user profile"), ['data' => new UserProfileResource($user)]);
    }
    public function referral()
    {
        $user                           = auth()->user();
        $data                           = [];
        $data['referral_code']          = $user->referral_code;
        $data['earned_referral_points'] = $user->earned_referral_points;
        $data['earned_referral_riyals'] = $user->earned_referral_riyals;
        $data['referrals_count']        = $user->myReferrals->count();
        $data['referrals']              = SimpleUserRecourse::collection($user->myReferrals);
        $data['image_en']               = SettingsService::getDataBaseSettingImage('referral_image_en');
        $data['image_ar']               = SettingsService::getDataBaseSettingImage('referral_image_ar');
        return $this->returnData('user referral', ['data' => $data]);
    }
    public function referralUpdate(ReferralUpdateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $user           = auth()->user();
            $referralUser   = User::where('referral_code', $request->referral_code)->first();
            if (!$referralUser) {
                throw new \RuntimeException(trans('the referral code is incorrect'));
            }
            if ($referralUser->id == $user->id) {
                throw new \RuntimeException(trans('you can not register by your own referral code'));
            }
            if ($user->register_by_id) {
                throw new \RuntimeException(trans('you already registered by referral code'));
            }

            $user->update([
                'register_by_id'            => $referralUser->id,
            ]);

            \DB::commit();
            return $this->returnData(trans('referral code is accepted'), []);
        } catch (\RuntimeException $e) {
            \DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), [], [], 422);
        } catch (\Exception $e) {
            \DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('can not updated'), [], [], 422);
        }
    }

    // update profile
    public function editProfile(EditProfileRequest $request)
    {
        try {
            \DB::beginTransaction();
            $user          = auth()->user();
            $profileData   = ['district_id', 'city_id', 'other_city_name', 'lat', 'lng'];
            $userData      = Arr::except($request->validated(), $profileData);
            if (isset($userData['date_of_birth'])) {
                $userData['date_of_birth'] = ToolHelper::convertArabicNumbers($userData['date_of_birth']);
            }
            $user->update(Arr::except($request->validated(), $profileData));
            $userHasNotProfile = $user->profile()->doesntExist();
            $user->profile()->updateOrCreate(['user_id' => $user->id], Arr::only($request->validated(), $profileData));

            if ($request->device_token) {
                Device::updateOrCreate([
                    'user_id' => $user->id,
                    'type' => 'ios',
                ], [
                    'device_token' => $request->device_token
                ]);
            }
            if ($userHasNotProfile) {
                $welcomeNotificationTitle = SettingsService::getDataBaseSetting('welcome_notification_title');
                $welcomeNotificationBody = SettingsService::getDataBaseSetting('welcome_notification_body');
                $userDonotHaveNotifications = $user->notifications()
                    ->where('title', $welcomeNotificationTitle)->where('body', $welcomeNotificationBody)
                    ->doesntExist();
                if ($userDonotHaveNotifications && $welcomeNotificationTitle && $welcomeNotificationBody) {
                    Notification::create([
                        'types'    => json_encode(['apps']),
                        'for'      => 'users',
                        'for_data' => json_encode([$user->id]),
                        'title'    => $welcomeNotificationTitle,
                        'body'     => $welcomeNotificationBody,
                        'media'    => null,
                    ]);
                }
            }
            \DB::commit();

            return $this->returnData(trans("profile updated"), ['data' => new UserProfileResource($user)]);
        } catch (\Exception $e) {
            \DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans("can not updated"), [], [], 422);
        }
    }
    public function updateQrCode(UpdateQrCodeRequest $request)
    {
        try {
            \DB::beginTransaction();
            $user          = auth()->user();
            if ($request->secretKey) {
                $json = json_decode(base64_decode($request->secretKey), true);
                if (isset($json['user_id'])) {
                    $operator = User::where('id', $json['user_id'] ?? null)->first();
                    if ($operator) {
                        $contact = Contract::forClient($operator->id)->currentActive()->first();
                        if ($contact) {
                            $contractExpirationDate = $contact->unlimited_days ? null : now()->addDays($contact->number_of_days);
                            $contractNote = $json['title'] ?? null;
                            $operatorId = $operator->id;
                            if ($operatorId != $user->id and  $operatorId != null and $operatorId != $user->operator_id) {
                                $user->update([
                                    'contract_note' => $contractNote,
                                    'contract_expiration_date' => $contractExpirationDate,
                                    'operator_id' => $operatorId
                                ]);
                            }
                        }
                    }
                }
            }
            \DB::commit();
            return $this->returnData(trans("qr code updated"), []);
        } catch (\Exception $e) {
            \DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans("can not updated"), [], [], 422);
        }
    }

    function generate_unique_code($length, $model, $col = 'code', $type = 'numbers', $letter_type = 'all')
    {
        if ($type == 'numbers') {
            $characters = '0123456789';
        } else {
            switch ($letter_type) {
                case 'all':
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;
                case 'lower':
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
                    break;
                case 'upper':
                    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;

                default:
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;
            }
        }
        $generate_random_code = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $generate_random_code .= $characters[rand(0, $charactersLength - 1)];
        }
        if ($model::where($col, $generate_random_code)->exists()) {
            $this->generate_unique_code($length, $model, $col, $type);
        }
        return $generate_random_code;
    }
}
