<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Controller AdminAccountController
 * 
 * Mengelola pembaruan mandiri profil akun administrator yang sedang login (My Account / Edit Profile),
 * termasuk nama, email, username, dan perubahan password mandiri.
 */
class AdminAccountController extends Controller
{
    /**
     * Menampilkan halaman formulir edit profil akun admin yang sedang login.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request): View
    {
        return view('admin.account.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui detail profil akun admin yang sedang login.
     * Memerlukan konfirmasi password saat ini (current_password) jika ingin mengubah password baru.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Autogenerate username default dari email jika username kosong
        if (!$request->has('username')) {
            $username = $user->username ?: explode('@', $request->input('email') ?? $user->email)[0];
            $request->merge(['username' => $username]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        unset($data['current_password']);

        // Jika password tidak diisi, abaikan kolom password saat update database
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Akun admin berhasil diperbarui.');
    }
}
