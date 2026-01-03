<?php

namespace App\Traits;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait RateLimiterTrait
{
    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        $usernameField = config('auth.login.rate_limiting.username_field', 'email');

        return Str::transliterate(Str::lower($request->input($usernameField) . '|' . $request->ip()));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }
}
