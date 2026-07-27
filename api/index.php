<?php

// =============================================================================
// Vercel PHP Serverless Entry Point for Laravel
// =============================================================================
// This file is the entry point for Vercel's PHP runtime (vercel-php@0.9.0).
// It:
//   1. Installs error handlers that log to stderr (visible in Vercel logs)
//   2. Creates required temporary directories (/tmp is writable on Vercel)
//   3. Checks critical env vars like APP_KEY before bootstrapping Laravel
//   4. Forwards all requests to Laravel's standard public/index.php
// =============================================================================

// ---------------------------------------------------------------------------
// Step 1: Install global error handlers
// - set_error_handler(): converts PHP warnings/notices to exceptions
// - set_exception_handler(): catches uncaught exceptions, logs to stderr
// - register_shutdown_function(): catches fatal errors (E_ERROR, E_PARSE)
// All errors are logged to stderr, which appears in Vercel's Logs dashboard.
// ---------------------------------------------------------------------------

// Convert PHP errors to exceptions (excludes suppressed @ calls)
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});

// Catch uncaught exceptions so Vercel logs show what actually happened
set_exception_handler(function (Throwable $e): void {
    error_log('[VERCEL] UNCAUGHT EXCEPTION: ' . $e->getMessage());
    error_log('[VERCEL] in ' . $e->getFile() . ':' . $e->getLine());
    error_log('[VERCEL] Stack trace: ' . $e->getTraceAsString());

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
        ]);
    }
    exit(1);
});

// Catch fatal errors (E_ERROR, E_PARSE) that set_error_handler doesn't catch
register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log('[VERCEL] FATAL ERROR: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => $error['message'],
            ]);
        }
    }
});

// ---------------------------------------------------------------------------
// Step 2: Create required temporary directories
// Vercel's serverless runtime has a writable /tmp directory.
// Laravel needs these for compiled views, cache, and sessions.
// ---------------------------------------------------------------------------

foreach (['/tmp/views', '/tmp/cache', '/tmp/sessions'] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// ---------------------------------------------------------------------------
// Step 3: Auto-generate APP_KEY if missing
// Laravel's Encrypter throws without APP_KEY. Since SESSION_DRIVER=array
// doesn't need persistent encryption, we generate a per-request key.
// For production with cookie sessions, set a real APP_KEY in Vercel env.
// ---------------------------------------------------------------------------

if (!getenv('APP_KEY') && !($_ENV['APP_KEY'] ?? null)) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    putenv("APP_KEY=$key");
    $_ENV['APP_KEY'] = $key;
    $_SERVER['APP_KEY'] = $key;
    error_log('[VERCEL] APP_KEY auto-generated (per-request). Set a permanent one in Vercel dashboard for persistent encryption.');
}

// ---------------------------------------------------------------------------
// Step 4: Bootstrap Laravel
// ---------------------------------------------------------------------------

require __DIR__ . '/../public/index.php';
