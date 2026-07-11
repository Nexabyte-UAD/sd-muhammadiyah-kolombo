<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Controller UserController
 * 
 * Mengelola pendaftaran dan CRUD akun pengguna administrator (Admin)
 * yang memiliki hak akses masuk ke panel backend admin website sekolah.
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh akun admin di sistem.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir pendaftaran akun admin baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan akun admin baru ke database.
     * Memiliki aturan pembuatan password yang aman (minimal 12 karakter, huruf besar-kecil, angka, simbol).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
        // Jika username tidak diisi, ambil nama depan email sebagai username default
        if (!$request->has('username') && $request->has('email')) {
            $request->merge(['username' => explode('@', $request->email)[0]]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:50|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        User::create([
            'name' => $formatter->name($request->name),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Admin',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit akun admin tertentu.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui detail akun admin di database.
     * Password bersifat opsional untuk diperbarui (hanya diproses jika diisi).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user, IndonesianTextFormatter $formatter)
    {
        if (!$request->has('username')) {
            $username = $user->username ?: explode('@', $request->input('email') ?? $user->email)[0];
            $request->merge(['username' => $username]);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        // Validasi password baru jika kolom diisi
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()];
        }

        $request->validate($rules);

        $data = [
            'name' => $formatter->name($request->name),
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus akun admin tertentu.
     * Mencegah admin menghapus akunnya sendiri yang sedang aktif digunakan login.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
