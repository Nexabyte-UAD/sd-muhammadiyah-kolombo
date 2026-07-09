<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        if ($request->is('login') || $request->is('portal-sdmuh*') || $request->routeIs('login')) {
            $response->headers->set('X-Frame-Options', 'DENY');
        } else {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $appHost = $request->getHost();
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self' https://{$appHost}; base-uri 'self'; form-action 'self' https://{$appHost}; frame-ancestors 'self'; ".
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://{$appHost}; ".
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://{$appHost}; ".
            "font-src 'self' data: https://cdn.jsdelivr.net https://fonts.gstatic.com; ".
            "img-src 'self' data: blob: https://{$appHost} https://images.unsplash.com; connect-src 'self' https://{$appHost}; ".
            "frame-src 'self' https://maps.google.com https://www.google.com"
        );

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
