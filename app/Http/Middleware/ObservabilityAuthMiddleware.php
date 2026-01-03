<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ObservabilityAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authBase = 'observability-auth';

        $authEnabled = (bool) config('observability.auth.enabled', true);
        if (! $authEnabled) {
            return $next($request);
        }

        if (session('observability_authed') === true) {
            return $next($request);
        }

        // Check for remember cookie
        $cookieEmail = $request->cookie('observability_remember');
        $configEmail = config('observability.auth.email');
        if ($cookieEmail && $configEmail && $cookieEmail === $configEmail) {
            session(['observability_authed' => true, 'observability_email' => $cookieEmail]);

            return $next($request);
        }

        if ($request->is($authBase . '/login') || $request->is($authBase . '/login/*') || $request->is($authBase . '/logout')) {
            return $next($request);
        }

        return redirect()->to(url($authBase . '/login'));
    }
}
