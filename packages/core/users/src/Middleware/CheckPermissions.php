<?php

namespace Core\Users\Middleware;

use Closure;
use Core\Users\Models\User;
use Illuminate\Http\Request;

class CheckPermissions
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
        $user = $request->user();
        // $user = User::first();
        // dd($user,$user->permissions,$user->can($request->route()->getName()),$request->route()->getName());
        if (! $request->user() || ! $request->user()->can($request->route()->getName())) {
            abort(403, 'access denied...  you Are frodiiden from this action');
        }
        return $next($request);
    }
}
