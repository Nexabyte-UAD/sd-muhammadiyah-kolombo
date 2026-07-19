<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        $settingsArray = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settingsArray = \App\Models\Setting::pluck('value', 'key')->toArray();
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum siap/migrate
        }
        \Illuminate\Support\Facades\View::share('settings', $settingsArray);

        // Chatbot Rate Limiter
        RateLimiter::for('chatbot', function (Request $request) {
            $sessionId = $request->hasSession() ? $request->session()->getId() : 'no-session';
            $visitorKey = hash('sha256', $request->ip().'|'.$sessionId);

            return [
                Limit::perMinute(5)->by('minute:'.$visitorKey)->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak permintaan. Silakan tunggu 1 menit.',
                    ], 429, $headers);
                }),
                Limit::perHour(30)->by('hour:'.$visitorKey)->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda telah mencapai batas maksimum pesan per jam. Silakan coba lagi nanti.',
                    ], 429, $headers);
                }),
            ];
        });
    }
}
