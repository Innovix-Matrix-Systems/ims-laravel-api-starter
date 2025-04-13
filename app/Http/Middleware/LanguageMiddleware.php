<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to get the locale from the Accept-Language header, or use app.locale, or fallback_locale if not set
        $locale = explode(',', $request->header('Accept-Language'))[0]
            ?? config('app.locale')
            ?? config('app.fallback_locale');

        // Check if the locale is supported or is a wildcard ("*")
        if ($locale === '*' || ! in_array($locale, config('app.supported_locales', []))) {
            $locale = config('app.fallback_locale', config('app.locale')); // Fallback to default locale if unsupported
        }

        app()->setLocale($locale);  // Set the app's locale
        Carbon::setLocale($locale);  // Set Carbon's locale

        return $next($request);  // Continue processing the request
    }
}
