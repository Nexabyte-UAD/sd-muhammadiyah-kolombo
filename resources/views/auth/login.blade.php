<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Administrator · {{ config('app.name', 'Sekolah') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-brand-panel">
            <div class="login-brand-content">
                <span class="login-brand-mark">MK</span>
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
                    <div class="admin-alert admin-alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-field login-field">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control-admin @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" autocomplete="username" autofocus required>
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-field login-field">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password"
                               class="form-control-admin @error('password') is-invalid @enderror"
                               autocomplete="current-password" required>
                        @error('password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <button type="submit" class="btn-admin login-submit">Masuk ke Panel Admin</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
