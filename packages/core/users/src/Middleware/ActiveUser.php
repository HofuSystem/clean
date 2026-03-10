<?php

namespace Core\Users\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || !($request->user()->is_active )){
            if(str_contains("application/json", strtolower($request->header("Accept")))) {
                return response()->json([
                    "status"=>false,
                    "message"=>"your account has been deactiveated...  contac us to reActivate it",
                ]);
            }
            // abort(403, 'your account has been deactiveated...  contact us to reActivate it');
        }
        return $next($request);
    }
}
