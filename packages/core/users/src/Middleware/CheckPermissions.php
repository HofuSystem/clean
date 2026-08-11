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
        if (!$user) {
            abort(403, 'access denied...  you Are frodiiden from this action');
        }

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        $hasPermission = $user->can($routeName)
            || (str_ends_with($routeName, '.update-password') && $user->can(str_replace('.update-password', '.edit', $routeName)))
            || (str_ends_with($routeName, '.profile.edit') && $user->can(str_replace('.profile.edit', '.edit', $routeName)));

        if (!$hasPermission) {
            abort(403, 'access denied...  you Are frodiiden from this action');
        }
        return $next($request);
    }
}
