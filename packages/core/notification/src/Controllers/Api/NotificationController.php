<?php

namespace Core\Notification\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\Notification\DataResources\Api\NotificationsResource;
use Core\Notification\Models\Notification;
use Core\Notification\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\ProviderResource;
use Core\Users\DataResources\UserProfileResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user                   = auth('api')->user();
            $notifications          = $user->notifications()->where('types','like','%apps%')->latest()->paginate(10);
            $unreadNotifications    = $user->unreadNotifications;
             foreach ($unreadNotifications as $notification){
                $notification->pivot->update(['read_at' => now()]);
            }
            return $this->returnData(trans('notifications'),['data' => NotificationsResource::collection($notifications)]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }


    public function show($id)
    {
        $notification = auth('api')->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }
        return $this->returnData(trans('notification'),['data'=>new NotificationsResource($notification)]);

    }


    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return $this->returnSuccessMessage(trans('deleted successfully'));
    }

    public function allow_notify(Request $request){
        $user = auth('api')->user();
        if($user->is_allow_notify == true){
            $user->update(['is_allow_notify'=>false]);
        }else{
            $user->update(['is_allow_notify'=>true]);
        }
        $telegramNotificationService = new TelegramNotificationService();
        $telegramNotificationService->sendMessage("@cleanstationsupport", $telegramNotificationService->clientChangeNotificationSettingsMessage($user, $user->is_allow_notify ? 'enabled' : 'disabled'));
        if($user->hasRole('client')){
            return $this->returnData(trans('update successfully'),['data'=>new UserProfileResource($user)]);
        }else{
            return $this->returnData(trans('update successfully'),[new ProviderResource($user)]);
        }
    }
}
