<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login Admin') · {{ config('app.name', 'Sekolah') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
</head>
<body class="login-page">
    <main class="auth-shell">
        <a href="{{ route('home') }}" class="auth-back-link">← Kembali ke website</a>

        <section class="auth-card">
            <img src="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}"
                 class="auth-logo"
                 alt="Logo SD Muhammadiyah Komplek Kolombo">

            <header class="auth-heading">
                <h1>@yield('heading')</h1>
                <p>@yield('description')</p>
            </header>

            @if(session('status'))
                <div class="auth-alert auth-alert-success" role="status">{{ session('status') }}</div>
            @endif

            @yield('content')
        </section>

        <p class="auth-security-note">
            <span aria-hidden="true">🔒</span>
            Akses terbatas untuk administrator yang berwenang.
        </p>
    </main>

    @stack('scripts')
</body>
</html>
