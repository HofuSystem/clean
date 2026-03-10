<?php

namespace Core\Users\Middleware;

use Closure;

use Core\Users\Models\Device;
use Core\Users\Models\User;

class MayLogin
{
    public function handle($request, Closure $next)
    {
        $token = substr(request()->header('authorization'), 7);
        $device_unique_id = request()->header('device-unique-id');
        $device = Device::where('login_token',$token)->where('unique_id',$device_unique_id)->first();

        if(isset($device) and  $device->login_token_status){
            $user = User::find($device->user_id);
            request()->merge(['user' => $user ]);
            request()->setUserResolver(function () use ($user){return $user;});
        }

        return $next($request);
    }
}
