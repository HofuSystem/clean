<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoIndexPrivatePages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $path = '/' . ltrim($request->path(), '/');

        if (preg_match('#^/(?:ar|en)?/?(?:admin(?:/|$)|login(?:/|$)|register(?:/|$)|password(?:/|$)|email/verify(?:/|$)|business(?:/|$))#i', $path)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
