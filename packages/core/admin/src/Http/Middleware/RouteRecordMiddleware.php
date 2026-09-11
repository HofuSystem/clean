<?php

namespace Core\Admin\Http\Middleware;

use Closure;
use Core\Admin\Services\RouteRecordsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RouteRecordMiddleware
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
        $response = $next($request);
        
        // Record the route after the request is processed
        try {
            RouteRecordsService::registerRequest($request);
        } catch (\Throwable $exception) {
            // A completed operation must not appear to fail because recording
            // failed: the customer could retry a successful payment or order.
            try {
                Log::warning('API usage recording failed', [
                    'exception' => get_class($exception),
                    'route' => $request->route()?->getName(),
                    'method' => $request->method(),
                ]);
            } catch (\Throwable) {
                // Preserve the response even when the log backend is unavailable.
            }
        }
        
        return $response;
    }
} 