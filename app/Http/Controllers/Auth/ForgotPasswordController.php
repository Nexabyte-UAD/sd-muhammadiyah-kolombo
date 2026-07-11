<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * Controller ForgotPasswordController
 * 
 * Mengelola permintaan tautan reset password (lupa password) dengan mengirimkan
 * email berisi token/tautan reset yang aman ke alamat email admin yang terdaftar.
 */
class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan halaman formulir permintaan reset password.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Mengirimkan link tautan reset password ke alamat email yang diminta.
     * Mengembalikan pesan sukses seragam (status) demi mencegah enumeration attack (keamanan email).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Kirim email reset password menggunakan broker default Laravel
        Password::sendResetLink($request->only('email'));

        return back()->with(
            'status',
            'Jika email terdaftar, tautan reset password akan dikirim.'
        );
    }
}
