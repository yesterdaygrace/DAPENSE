<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('export', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('import', function (Request $request) {
            return Limit::perMinute(2)
                ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('posting', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
