<?php

// =============================================================================
// Vercel PHP Serverless Entry Point for Laravel
// =============================================================================
// This file is the entry point for Vercel's PHP runtime (vercel-php@0.7.4+).
// It creates the required temporary directories and forwards all requests
// to Laravel's standard public/index.php.
// =============================================================================

// Create required temporary directories for Vercel's read-only filesystem
$tmpDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward all requests to Laravel's standard entry point
require __DIR__ . '/../public/index.php';
