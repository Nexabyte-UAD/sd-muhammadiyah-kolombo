<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware SecurityHeaders
 * 
 * Middleware untuk menyuntikkan header keamanan HTTP penting (seperti CSP, HSTS, X-Frame-Options, dll.)
 * pada setiap respon server untuk mengamankan situs web dari serangan Clickjacking, XSS, CSRF, dan MIME sniffing.
 */
class SecurityHeaders
{
    /**
     * Menangani request yang masuk dan menyematkan header keamanan pada responnya.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah browser menebak tipe MIME berkas (MIME Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Proteksi Clickjacking: larang pemuatan situs di dalam iframe/frame luar pada halaman login
        if ($request->is('login') || $request->is('portal-sdmuh*') || $request->routeIs('login')) {
            $response->headers->set('X-Frame-Options', 'DENY');
        } else {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }
        
        // Batasi kebocoran informasi pengirim pada header HTTP Referer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Membatasi akses sensor perangkat keras client (kamera, mikrofon, lokasi geografis) demi privasi
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Menerapkan Content Security Policy (CSP) untuk membatasi asal sumber file skrip, css, font, gambar, iframe
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

        // Aktifkan HSTS (HTTP Strict Transport Security) jika koneksi menggunakan protokol HTTPS aman
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
