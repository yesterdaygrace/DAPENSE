<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBfcache;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
            'no-cache' => PreventBfcache::class,
        ]);

        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->dontReportDuplicates();

        // Friendly 503 for DB unreachable on web routes (avoids leaking host/creds even with APP_DEBUG)
        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return;
            }

            report($e);

            $hint = config('database.connections.mysql.host') === 'dapense-mysql' && ! config('app.docker')
                ? 'DB host dapense-mysql is only reachable inside Docker. Set DB_HOST=127.0.0.1 and DB_PORT=13306 or run `docker compose up`.'
                : 'Database unavailable. Check DB_HOST/DB_PORT and that MySQL is running.';

            return response()->view('errors.db-unavailable', [
                'hint' => $hint,
                'message' => $e->getMessage(),
            ], 503);
        });

        $exceptions->render(function (PDOException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return;
            }

            report($e);

            return response()->view('errors.db-unavailable', [
                'hint' => 'Database unavailable. Check DB connection settings.',
                'message' => $e->getMessage(),
            ], 503);
        });
    })->create();
