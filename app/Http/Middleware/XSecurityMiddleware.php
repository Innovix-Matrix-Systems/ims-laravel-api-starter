<?php

namespace App\Http\Middleware;

use App\Traits\RateLimitterTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XSecurityMiddleware
{
    use RateLimitterTrait;
    const MAX_ATTEMPTS = 5;
    const MAX_DECAY_MINUTES = 1;
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.xsecure_enabled')) {
            return $next($request);
        }
        if ($this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            self::MAX_ATTEMPTS,
        )) {
            return response()->json(['error' => 'Too many requests. Please try again later.'], 429);
        }

        // Check CSRF token validity
        if (!$this->isValidXSecureToken($request->header('X-SECURITY-TOKEN') ?? '')) {
            // Increment failed attempts for the client
            $this->limiter()->hit($this->throttleKey($request), self::MAX_DECAY_MINUTES * 60);

            return response()->json(['error' => 'Invalid XSECURE token'], 403);
        }

        // Reset failed attempts for the client
        $this->limiter()->clear($this->throttleKey($request));

        return $next($request);
    }
    /**
     * Validate XSECURE token against the shared secret key.
     *
     * @param  string $token
     * @return bool
     */

    private function isValidXSecureToken($signature)
    {
        $sharedSecretKey = config('app.xsecure_secret');
        $sharedSecretToken = config('app.xsecure_token');

        $expectedSignature = hash_hmac('sha256', $sharedSecretToken, $sharedSecretKey);
        return hash_equals($expectedSignature, $signature);
    }
}
