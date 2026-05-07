<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCache
{
    /**
        * Set no-cache headers to prevent sensitive pages from being cached
        * and re-shown after logout.
        */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
