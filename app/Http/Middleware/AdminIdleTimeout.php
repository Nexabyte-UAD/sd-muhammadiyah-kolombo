<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware AdminIdleTimeout
 * 
 * Mengelola deteksi keheningan (idle timeout) administrator. Jika admin tidak melakukan aktivitas
 * apa pun di halaman backend selama batas waktu tertentu (default 30 menit), sistem akan mengeluarkan
 * admin secara otomatis dan mengarahkan kembali ke halaman login demi alasan keamanan sesi.
 */
class AdminIdleTimeout
{
    /**
     * Menangani request masuk dan mengecek durasi keheningan aktivitas.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hitung batas waktu keheningan (idle timeout) dalam detik (konfigurasi dalam menit)
        $timeout = max(1, (int) config('auth.admin_idle_timeout', 30)) * 60;
        $lastActivity = (int) $request->session()->get('admin_last_activity', time());

        // Jika selisih waktu saat ini dengan aktivitas terakhir melebihi batas timeout
        if (time() - $lastActivity > $timeout) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Sesi berakhir karena tidak ada aktivitas. Silakan masuk kembali.');
        }

        // Simpan waktu aktivitas terakhir terbaru ke session
        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }
}
