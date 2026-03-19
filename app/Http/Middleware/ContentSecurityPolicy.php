<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com https://connect.facebook.net https://www.instagram.com https://cdn.jsdelivr.net https://checkout.razorpay.com https://*.razorpay.com",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com",
            "img-src 'self' data: blob: https: http:",
            "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com",
            "connect-src 'self' https://www.google-analytics.com https://www.facebook.com https://graph.facebook.com https://graph.instagram.com https://api.razorpay.com https://api.postalpincode.in https://track.delhivery.com",
            "frame-src 'self' https://www.instagram.com https://api.razorpay.com https://checkout.razorpay.com https://*.razorpay.com https://www.google.com https://accounts.google.com https://www.facebook.com",
            "media-src 'self' https: blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://api.razorpay.com https://accounts.google.com https://www.facebook.com",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
