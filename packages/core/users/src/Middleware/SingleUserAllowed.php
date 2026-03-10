<?php

namespace Core\Users\Middleware;

use Carbon\Carbon;
use Closure;
use Core\Entities\Models\Activity;

class SingleUserAllowed
{
    public function handle($request, Closure $next)
    {
        $activity = Activity::where('user_id','!=',request()->user()->id)->where(['url'=> url()->current()])->orderBy('updated_at', 'asc')->first();
        if($activity){
            $timeFirst  = strtotime($activity->updated_at->toDateTimeString());
            $timeSecond = strtotime(Carbon::now()->toDateTimeString());
            $differenceInSeconds =  abs( $timeSecond - $timeFirst );
           if($differenceInSeconds < 20){
            session()->flash('message', trans('one User is allowed at a time'));
            return redirect()->back();
           }
        }
        return $next($request);
    }
}
