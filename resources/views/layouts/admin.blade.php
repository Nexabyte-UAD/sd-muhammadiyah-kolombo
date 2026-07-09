<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') - {{ config('app.name', 'Sekolah') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-shell">
        @include('layouts.admin.sidebar')

        <div class="admin-main">
            @include('layouts.admin.header')

            <main class="admin-content">
                <div class="admin-container">
                    @if(trim($__env->yieldContent('content_header')) !== '' && trim($__env->yieldContent('page_title')) === '')
                        <div class="legacy-page-heading">
                            @yield('content_header')
                        </div>
                    @else
                        <div class="page-heading">
                            <div>
                                <div class="page-kicker">@yield('page_kicker', 'Panel Admin')</div>
                                <h1 class="page-title">{{ trim($__env->yieldContent('page_title', $__env->yieldContent('title', 'Dashboard'))) }}</h1>
                                @hasSection('page_description')
                                    <p class="page-description">@yield('page_description')</p>
                                @endif
                            </div>
                            <div class="page-actions">
                                @yield('page_actions')
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="admin-alert admin-alert-success" role="alert">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="admin-alert admin-alert-danger" role="alert">{{ session('error') }}</div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="sidebar-backdrop" data-sidebar-close></div>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/admin-panel.js') }}"></script>
    @stack('js')
    @stack('scripts')
</body>
</html>
