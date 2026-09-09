<?php

namespace Core\Users\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->is_active) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'your account has been deactiveated...  contac us to reActivate it',
                ], 403);
            }

            abort(403, 'your account has been deactiveated...  contact us to reActivate it');
        }

        return $next($request);
    }
}
