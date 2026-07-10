<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Halaman login administrator portal akademik dan manajemen website SD Muhammadiyah Komplek Kolombo Yogyakarta.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Administrator · {{ config('app.name', 'Sekolah') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-brand-panel" aria-label="Informasi panel admin">
            <div class="login-brand-content">
                <img src="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}"
                     alt="Logo SD Muhammadiyah Komplek Kolombo"
                     class="login-brand-logo">
                <p class="login-eyebrow">Panel Administrasi</p>
                <h1>Kelola website sekolah dengan lebih sederhana.</h1>
                <p>Perbarui informasi akademik dan konten publik dari satu ruang kerja yang aman.</p>
            </div>
        </section>

        <section class="login-form-panel">
            <div class="login-card">
                <a href="{{ route('home') }}" class="login-back-link">← Kembali ke website</a>

                <div class="login-heading">
                    <h2>Selamat datang</h2>
                    <p>Masuk menggunakan akun administrator.</p>
                </div>

                @if(session('status'))
                    <div class="admin-alert admin-alert-success" role="status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-field login-field">
                        <label for="login" class="form-label">Username atau Email</label>
                        <input type="text" name="login" id="login"
                               class="form-control-admin @error('login') is-invalid @enderror"
                               value="{{ old('login') }}"
                               autocomplete="username"
                               autofocus required>
                        @error('login')<div class="form-error" role="alert">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-field login-field">
                        <div class="auth-label-row">
                            <label for="password" class="form-label">Password</label>
                            <a href="{{ route('password.request') }}">Lupa password?</a>
                        </div>
                        <div style="position: relative;">
                            <input type="password" name="password" id="password"
                                   class="form-control-admin @error('password') is-invalid @enderror"
                                   autocomplete="current-password" required style="padding-right: 42px;">
                            <button type="button" id="toggle-password" data-password-toggle="password"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; font-size: 15px; line-height: 1;"
                                    aria-label="Tampilkan password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')<div class="form-error" role="alert">{{ $message }}</div>@enderror
                    </div>

                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <button type="submit" class="btn-admin login-submit">Masuk ke Panel Admin</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        const btn = document.getElementById('toggle-password');
        const input = document.getElementById('password');
        btn.addEventListener('click', function () {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>
</html>


