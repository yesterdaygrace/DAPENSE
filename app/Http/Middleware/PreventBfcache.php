<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBfcache
{
    /**
     * Prevent browser back/forward cache (bfcache).
     *
     * bfcache preserves the page's JavaScript state including Livewire's
     * component instances. When navigating back to a cached Livewire page,
     * the component may rehydrate on stale DOM, causing duplicate content.
     *
     * These headers force the browser to always request a fresh page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
