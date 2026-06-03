<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarketingEngineAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the valid token from config or .env, defaulting to a fallback string
        $validToken = config('services.marketing_engine.api_key', env('MARKETING_ENGINE_API_KEY', 'default-marketing-api-key-12345'));

        // Check for Bearer Token or X-API-Key header
        $providedToken = $request->bearerToken() ?? $request->header('X-API-Key');

        if (!$providedToken || $providedToken !== $validToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing API token.'
            ], 401);
        }

        return $next($request);
    }
}
