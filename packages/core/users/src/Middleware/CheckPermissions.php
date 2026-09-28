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

        // Preserve the media permission already assigned to existing staff.
        if (in_array($routeName, [
            'dashboard.media-center.list',
            'dashboard.media-center.add-new',
            'dashboard.media-center.delete',
        ], true)) {
            $possiblePermissions[] = 'dashboard.mediacenter.mymedia';
        }

        if (str_ends_with($routeName, '.update-password')) {
            $possiblePermissions[] = str_replace('.update-password', '.edit', $routeName);
            $possiblePermissions[] = str_replace('dashboard.', '', str_replace('.update-password', '.edit', $routeName));
            $possiblePermissions[] = str_replace('.', ' ', str_replace('dashboard.', '', str_replace('.update-password', '.edit', $routeName)));
        }

        if (str_ends_with($routeName, '.profile.edit')) {
            $possiblePermissions[] = str_replace('.profile.edit', '.edit', $routeName);
            $possiblePermissions[] = str_replace('dashboard.', '', str_replace('.profile.edit', '.edit', $routeName));
        }

        // Notification dashboard route aliases mapped to existing permissions
        if ($routeName === 'dashboard.notifications.previewAudience') {
            $possiblePermissions[] = 'dashboard.notifications.create';
            $possiblePermissions[] = 'notifications.create';
            $possiblePermissions[] = 'notifications create';
        }
        if ($routeName === 'dashboard.notifications.queueMonitor') {
            $possiblePermissions[] = 'dashboard.notifications.index';
            $possiblePermissions[] = 'notifications.index';
            $possiblePermissions[] = 'notifications index';
        }
        if ($routeName === 'dashboard.notifications.exportDetails') {
            $possiblePermissions = [
                'dashboard.notifications.export',
                'notifications.export',
                'notifications export',
            ];
        }
        if ($routeName === 'dashboard.notifications.retryTransient') {
            $possiblePermissions[] = 'dashboard.notifications.edit';
            $possiblePermissions[] = 'notifications.edit';
            $possiblePermissions[] = 'notifications edit';
            $possiblePermissions[] = 'dashboard.notifications.resendPending';
        }
        if (in_array($routeName, ['dashboard.notifications.getEligibilityUsers', 'dashboard.notifications.getDeviceResults'], true)) {
            $possiblePermissions[] = 'dashboard.notifications.show';
            $possiblePermissions[] = 'notifications.show';
            $possiblePermissions[] = 'notifications show';
        }

        foreach ($possiblePermissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'access denied...  you Are frodiiden from this action');
    }
}

