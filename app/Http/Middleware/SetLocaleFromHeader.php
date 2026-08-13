<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Supported locales in the application.
     */
    protected array $supportedLocales = ['ar', 'en'];

    /**
     * Handle an incoming request.
     * Reads the Accept-Language header and sets the app locale accordingly.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Resolve the locale from the request.
     * Checks Accept-Language header and falls back to app default.
     */
    protected function resolveLocale(Request $request): string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if ($acceptLanguage) {
            // Parse "ar", "en", "ar-SA", "en-US,en;q=0.9,ar;q=0.8" etc.
            $languages = explode(',', $acceptLanguage);

            foreach ($languages as $language) {
                // Strip quality values (e.g. "en;q=0.9" -> "en")
                $lang = trim(explode(';', $language)[0]);

                // Take only the primary subtag (e.g. "ar-SA" -> "ar")
                $primaryLang = strtolower(explode('-', $lang)[0]);

                if (in_array($primaryLang, $this->supportedLocales)) {
                    return $primaryLang;
                }
            }
        }

        return config('app.locale', 'ar');
    }
}
