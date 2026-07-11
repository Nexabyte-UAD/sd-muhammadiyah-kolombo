<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller AuthenticatedSessionController
 * 
 * Mengelola alur autentikasi/login pengguna admin, perekaman waktu login,
 * pencatatan alamat IP login, inisialisasi aktivitas log sesi, dan proses logout admin.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman formulir login admin.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses percobaan login admin (autentikasi kredensial).
     * Melakukan regenerasi ID session untuk mencegah session fixation, 
     * mencatat timestamp login terbaru, IP address, serta menulis riwayat log audit.
     * 
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Jalankan proses autentikasi (kredensial dicocokkan & rate limiting diperiksa)
        $request->authenticate();

        // Regenerasi ID Session dan simpan timestamp aktivitas terakhir untuk Idle Timeout
        $request->session()->regenerate();
        $request->session()->put('admin_last_activity', time());

        $user = $request->user();
        // Rekam info login teranyar ke user
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action_type' => 'Login',
            'module' => 'Autentikasi',
            'description' => 'Login pengguna berhasil dari IP '.$request->ip().'.',
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Menghancurkan session login admin (proses Logout).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action_type' => 'Logout',
                'module' => 'Autentikasi',
                'description' => 'Admin keluar dari sistem.',
            ]);
        }

        Auth::guard('web')->logout();

        // Batalkan sesi saat ini dan buat ulang CSRF token baru demi keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
