<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * Controller ResetPasswordController
 * 
 * Mengelola pemrosesan pembuatan password baru (Reset Password) bagi pengguna admin
 * yang telah memverifikasi token reset password yang dikirim ke email mereka.
 */
class ResetPasswordController extends Controller
{
    /**
     * Menampilkan halaman formulir setel ulang (reset) password baru.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token  Token verifikasi reset password
     * @return \Illuminate\View\View
     */
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    /**
     * Memproses penggantian password baru setelah memvalidasi token reset dan email.
     * Menggunakan aturan password kuat (minimal 12 karakter, huruf besar-kecil, angka, simbol).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        // Eksekusi penggantian password menggunakan Laravel Password broker
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Jika penggantian gagal karena token kedaluwarsa atau tidak cocok
        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Tautan reset password tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui. Silakan masuk.');
    }
}
