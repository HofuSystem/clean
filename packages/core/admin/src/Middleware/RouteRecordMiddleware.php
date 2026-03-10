<?php

namespace Core\Admin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Core\Admin\Services\RouteRecordsService;

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
        // Register the request after the response is ready
        RouteRecordsService::registerRequest($request);
        return $next($request);
        
    }
} 