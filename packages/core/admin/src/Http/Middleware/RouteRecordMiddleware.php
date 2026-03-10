<?php

namespace Core\Admin\Http\Middleware;

use Closure;
use Core\Admin\Services\RouteRecordsService;
use Illuminate\Http\Request;

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
        RouteRecordsService::registerRequest($request);
        
        return $response;
    }
} 