<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        //
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(maxAttempts: config('app.rate_limit_max_attempts_per_minute', 1000))
                ->by($request->user()?->id ?: $request->ip());
        });

        // Gate::define('viewPulse', function (User $user) {
        //     return $user->isAdmin();
        // });
    }
}
