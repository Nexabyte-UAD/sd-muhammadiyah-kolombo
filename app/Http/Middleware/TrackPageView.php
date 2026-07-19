<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware TrackPageView
 * 
 * Mencatat setiap kunjungan halaman publik (GET request yang berhasil/200)
 * ke tabel page_views. Hanya aktif di route publik, bukan di route admin.
 * Bot/crawler dideteksi dan dilewati agar data lebih akurat.
 */
class TrackPageView
{
    /**
     * Daftar substring user-agent yang terindikasi bot/crawler.
     * Kunjungan dari agen ini tidak dicatat.
     */
    private const BOT_AGENTS = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners',
        'googlebot', 'bingbot', 'yandexbot', 'duckduckbot',
        'facebookexternalhit', 'ia_archiver', 'curl', 'wget',
        'python', 'go-http', 'axios', 'httpclient',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat GET request yang sukses (HTTP 200)
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return $response;
        }

        // Lewati kunjungan bot/crawler
        $userAgent = strtolower($request->userAgent() ?? '');
        foreach (self::BOT_AGENTS as $bot) {
            if (str_contains($userAgent, $bot)) {
                return $response;
            }
        }

        $path = '/' . ltrim($request->path(), '/');

        // Catat kunjungan ke database (gunakan try-catch agar tidak merusak halaman jika gagal)
        try {
            PageView::create([
                'page'       => $path,
                'page_label' => PageView::resolveLabel($path),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 300),
            ]);
        } catch (\Throwable) {
            // Diam-diam abaikan error tracking agar tidak mengganggu pengunjung
        }

        return $response;
    }
}
