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

        if ($user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('it')) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        if (!$routeName) {
            return $next($request);
        }

        $cleanRoute = str_replace('dashboard.', '', $routeName);
        $spaceRoute = str_replace('.', ' ', $cleanRoute);

        $possiblePermissions = [
            $routeName,
            $cleanRoute,
            $spaceRoute,
        ];

        if (str_ends_with($routeName, '.update-password')) {
            $possiblePermissions[] = str_replace('.update-password', '.edit', $routeName);
            $possiblePermissions[] = str_replace('dashboard.', '', str_replace('.update-password', '.edit', $routeName));
            $possiblePermissions[] = str_replace('.', ' ', str_replace('dashboard.', '', str_replace('.update-password', '.edit', $routeName)));
        }

        if (str_ends_with($routeName, '.profile.edit')) {
            $possiblePermissions[] = str_replace('.profile.edit', '.edit', $routeName);
            $possiblePermissions[] = str_replace('dashboard.', '', str_replace('.profile.edit', '.edit', $routeName));
        }

        foreach ($possiblePermissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'access denied...  you Are frodiiden from this action');
    }
}
