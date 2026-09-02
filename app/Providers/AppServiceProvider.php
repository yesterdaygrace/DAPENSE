<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS in non-local environments (required for Vercel)
        if ($this->app->environment('production') && ! config('app.docker')) {
            URL::forceScheme('https');
        }

        // Auto-detect Vercel serverless environment and configure drivers
        if (config('app.vercel') || config('app.vercel_env')) {
            $this->configureForVercel();
        }

        // Warn when host .env uses container-only DB host (common cause of "getaddrinfo for dapense-mysql failed")
        if (! config('app.docker') && config('database.connections.mysql.host') === 'dapense-mysql') {
            Log::warning('DB_HOST=dapense-mysql is only resolvable inside Docker. For `php artisan serve` on host, set DB_HOST=127.0.0.1 and DB_PORT=${DB_EXTERNAL_PORT:-13306} or run `docker compose up`.');
        }

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

    private function configureForVercel(): void
    {
        // These are not covered by vercel.json env block:
        config()->set('queue.default', 'sync');

        // Log Vercel detection for debugging
        error_log('[VERCEL] Vercel environment detected. Serverless config applied.');
    }
}
