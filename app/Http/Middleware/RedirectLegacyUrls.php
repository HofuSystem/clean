<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectLegacyUrls
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
        $path = $request->path(); // e.g. "ar/about-us" or "public/ar/services"
        
        $newPath = $path;
        $modified = false;
        
        // 1. Remove .html
        if (str_ends_with($newPath, '.html')) {
            $newPath = preg_replace('/\.html$/', '', $newPath);
            $modified = true;
        }
        
        // 2. Remove public/
        if (str_starts_with($newPath, 'public/')) {
            $newPath = preg_replace('/^public\//', '', $newPath);
            $modified = true;
        }
        
        // 3. Legacy map from the brief
        $legacyMap = [
            'ar/services/mens-laundry' => 'ar/services/wash-and-iron',
            'en/services/mens-laundry' => 'en/services/wash-and-iron',
            'ar/services/medical-military' => 'ar/b2b',
            'en/services/medical-military' => 'en/b2b',
            'ar/services/carpets-furnishings' => 'ar/services/carpet-upholstery-cleaning',
            'en/services/carpets-furnishings' => 'en/services/carpet-upholstery-cleaning',
            'ar/about-us' => 'ar/why-us',
            'en/about-us' => 'en/why-us',
        ];
        
        if (isset($legacyMap[$newPath])) {
            $newPath = $legacyMap[$newPath];
            $modified = true;
        }
        
        if ($modified) {
            $queryString = $request->getQueryString();
            $targetUrl = url($newPath === '/' ? '' : $newPath) . ($queryString ? '?' . $queryString : '');
            
            return redirect()->to($targetUrl, 301);
        }

        return $next($request);
    }
}
