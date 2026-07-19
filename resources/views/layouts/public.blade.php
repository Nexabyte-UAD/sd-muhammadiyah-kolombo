{{--
    Layout Utama Publik (layouts/public.blade.php)
    Mengatur kerangka dasar tampilan antarmuka pengunjung (frontend), termasuk top-bar
    info kontak cepat, tulisan berjalan (running text), navbar navigasi bertingkat (dropdown),
    konten utama halaman publik, peta lokasi Google Maps, link media sosial, serta penanggalan
    otomatis terformat Bahasa Indonesia di bagian top-bar.
--}}
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $settings['meta_description'] ?? 'Portal resmi SD Muhammadiyah Komplek Kolombo Yogyakarta. Temukan informasi pendaftaran, kegiatan akademik, prestasi, guru, dan berita terbaru sekolah.' }}">
    <title>{{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Komplek Kolombo' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
      body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        color: #334155;
        padding-top: 108px; /* Top bar 36px + navbar 72px */
        font-size: 0.95rem; /* Slightly larger than 0.9rem for readability, but proportional */
        line-height: 1.6;
        overflow-x: hidden;
      }

      img, svg, video, iframe {
        max-width: 100%;
      }

      main, section, .container, .container-fluid, .row, [class*="col-"] {
        min-width: 0;
      }

      p, h1, h2, h3, h4, h5, h6, a, td, th {
        overflow-wrap: anywhere;
      }
      
      @media (max-width: 991.98px) {
        body { padding-top: 108px; }
      }
      
      @media (max-width: 767.98px) {
        body { padding-top: 108px; }

        .navbar-collapse {
          max-height: calc(100vh - 108px);
          overflow-y: auto;
          overscroll-behavior: contain;
          padding: .75rem 0 1rem;
        }

        .navbar-nav .dropdown-menu {
          margin-left: .75rem;
          box-shadow: none !important;
        }

        main > section,
        main > .py-5 {
          scroll-margin-top: 108px;
        }

        footer iframe {
          min-height: 260px;
        }
      }

      @media (max-width: 374.98px) {
        .container {
          --bs-gutter-x: 1.5rem;
        }

        .navbar-brand {
          min-width: 0;
          max-width: calc(100% - 52px);
          margin-right: 0;
          gap: .4rem !important;
        }

        .navbar-brand img {
          width: 42px !important;
          height: 42px !important;
        }

        .navbar-brand-title {
          min-width: 0;
        }

        .navbar-brand-title strong {
          overflow: hidden;
          font-size: .72rem;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .navbar-brand-title small {
          display: none;
        }

        .navbar-toggler {
          flex: 0 0 auto;
          padding: .25rem .45rem;
        }

        .btn {
          white-space: normal;
        }
      }
      
      /* Global Premium Colors & Consistency */
      .text-dark { color: #0f172a !important; }       /* Slate 900 */
      .text-secondary { color: #475569 !important; }  /* Slate 600 */
      .text-primary { color: #172554 !important; }    /* Biru Utama */
      .bg-primary { background-color: #172554 !important; }
      .bg-light { background-color: #f8fafc !important; } /* Slate 50 */
      
      .btn-primary { 
          background-color: #172554 !important; 
          border-color: #172554 !important; 
          color: #ffffff !important;
      }
      .btn-primary:hover {
          background-color: #0b1120 !important;
          border-color: #0b1120 !important;
      }
      .btn-outline-primary {
          color: #172554 !important;
          border-color: #172554 !important;
      }
      .btn-outline-primary:hover {
          background-color: #172554 !important;
          color: #ffffff !important;
      }
      
      .top-bar {
        background-color: #172554;
        color: white;
        font-size: 0.85rem;
        z-index: 1040;
      }

      .top-bar a {
        color: white;
        text-decoration: none;
      }
      
      .navbar {
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        top: 36px; /* Below topbar */
      }
      
      .navbar-brand {
        font-weight: 800;
        color: #172554 !important;
      }

      .navbar-brand-title {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
        white-space: nowrap;
      }

      .navbar-brand-title strong {
        color: #172554;
        font-size: 0.92rem;
        letter-spacing: -0.01em;
      }

      .navbar-brand-title small {
        margin-top: 0.2rem;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.02em;
      }

      @media (min-width: 992px) and (max-width: 1199.98px) {
        .navbar-brand-title {
          display: none;
        }
      }

      @media (max-width: 374.98px) {
        .navbar-brand-title strong { font-size: 0.78rem; }
        .navbar-brand-title small { font-size: 0.64rem; }
      }

      .nav-link {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #0f172a !important;
        transition: color 0.3s;
      }

      .nav-link:hover, .nav-link.active {
        color: #172554 !important;
      }

      .scroll-reveal {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 650ms ease, transform 650ms cubic-bezier(.22, 1, .36, 1);
      }

      .scroll-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
      }

      @media (prefers-reduced-motion: reduce) {
        .scroll-reveal,
        .scroll-reveal.is-visible {
          opacity: 1;
          transform: none;
          transition: none;
        }
      }
      
      .dropdown-item.active, .dropdown-item:active {
        background-color: #172554;
        color: #ffffff;
      }
      
      @media all and (min-width: 992px) {
        .navbar .dropdown:hover .dropdown-menu {
          display: block;
          margin-top: 0;
        }
      }

      .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
      }

      @media (hover: none), (pointer: coarse) {
        .card:hover {
          transform: none;
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
      }

      .btn-primary {
        border-radius: 0.75rem;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
      }

      .section-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2.5rem;
      }
      
      footer {
        background-color: #172554; /* Disamakan dengan warna top-bar */
        color: #cbd5e1;
      }
    </style>
    @stack('styles')
  </head>
  <body class="d-flex flex-column min-vh-100">
    
    <!-- Top Bar -->
    <div class="top-bar py-2 fixed-top w-100">
      <div class="container d-flex justify-content-between align-items-center">
        
        <!-- Hari dan Tanggal Otomatis (Kiri) -->
        <div class="text-white pe-3 border-end border-white border-opacity-25 d-none d-sm-block" style="white-space: nowrap; font-size: 0.85rem; font-weight: 500;">
          <span id="topbar-date">Memuat...</span>
        </div>

        <!-- Tulisan Berjalan (Tengah) -->
        <div class="flex-grow-1 px-3 px-md-4 overflow-hidden align-items-center d-flex">
          <marquee behavior="scroll" direction="left" scrollamount="6" class="m-0 text-white" style="letter-spacing: 0.5px; font-size: 0.85rem; font-weight: 400;">
             Selamat Datang di Website Resmi {{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Komplek Kolombo' }} | Terdepan Dalam Mendidik Generasi Islami, Cerdas, Berprestasi, dan Berkarakter Mulia!
          </marquee>
        </div>

        <!-- Kontak Cepat (Kanan) -->
        <div class="d-none d-md-flex align-items-center gap-2 ps-3 border-start border-white border-opacity-25" style="white-space: nowrap; font-size: 0.85rem; font-weight: 500; color: white;">
          <x-admin-icon name="phone-out" size="16" class="text-warning me-1"/>
          {{ $settings['telepon'] ?? '(0274) 585755' }}
        </div>

      </div>
    </div>

    <nav class="navbar navbar-expand-lg fixed-top py-2">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
          <img src="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}"
               alt="Logo SD Muhammadiyah Komplek Kolombo"
               style="height: 56px; width: 56px; object-fit: contain;">
          <span class="navbar-brand-title">
            <strong>SD Muhammadiyah</strong>
            <small>Komplek Kolombo Yogyakarta</small>
          </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigasi">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto gap-2">
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('sambutan', 'tentang', 'visi-misi', 'akreditasi') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Profil
              </a>
              <ul class="dropdown-menu border-0 shadow-sm">
                <li><a class="dropdown-item" href="{{ route('sambutan') }}">Kata Sambutan</a></li>
                <li><a class="dropdown-item" href="{{ route('tentang') }}">Tentang</a></li>
                <li><a class="dropdown-item" href="{{ route('visi-misi') }}">Visi & Misi</a></li>
                <li><a class="dropdown-item" href="{{ route('akreditasi') }}">Akreditasi</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('prestasi') ? 'active' : '' }}" href="{{ route('prestasi') }}">Prestasi</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('guru') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Struktural
              </a>
              <ul class="dropdown-menu border-0 shadow-sm">
                <li><a class="dropdown-item" href="{{ route('guru', ['tipe' => 'guru']) }}">Guru</a></li>
                <li><a class="dropdown-item" href="{{ route('guru', ['tipe' => 'staf']) }}">Staf</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('siswa', 'kelas', 'alumni') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Kesiswaan
              </a>
              <ul class="dropdown-menu border-0 shadow-sm">
                <li><a class="dropdown-item" href="{{ route('siswa') }}">Data Siswa</a></li>
                <li><a class="dropdown-item" href="{{ route('kelas') }}">Data Kelas</a></li>
                <li><a class="dropdown-item" href="{{ route('alumni') }}">Data Alumni</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('berita') ? 'active' : '' }}" href="{{ route('berita') }}">Berita</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('ekstrakurikuler') ? 'active' : '' }}" href="{{ route('ekstrakurikuler') }}">Ekstrakurikuler</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('home') }}#footer">Kontak</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="flex-grow-1">
      @yield('content')
    </main>

    <footer id="footer" class="py-5 mt-5">
      <div class="container pb-4 border-bottom border-secondary border-opacity-25 mb-4">
        <div class="row g-4">
          <!-- Hubungi Kami -->
          <div class="col-lg-4 col-md-6">
            <h5 class="fw-bold text-white mb-4">Hubungi Kami</h5>
            <a href="https://www.google.com/maps/search/?api=1&query=SD+Muhammadiyah+Komplek+Kolombo" target="_blank" class="d-flex mb-3 text-decoration-none text-light hover-white align-items-start">
              <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor" class="text-warning me-3 flex-shrink-0" aria-hidden="true"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/></svg>
              <span>{{ $settings['alamat'] ?? 'Jl. Rajawali No. 10, Demangan Baru, Depok, Sleman, Yogyakarta' }}</span>
            </a>
            <div class="d-flex align-items-start mb-3">
              <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor" class="text-warning me-3 flex-shrink-0" aria-hidden="true"><path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg>
              <span class="text-light">{{ $settings['telepon'] ?? '(0274) 585755' }}</span>
            </div>
            <div class="d-flex align-items-start mb-4">
              <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor" class="text-warning me-3 flex-shrink-0" aria-hidden="true"><path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/></svg>
              <span class="text-light">{{ $settings['email'] ?? 'sdmuhkkolombo@gmail.com' }}</span>
            </div>
            <div class="d-flex gap-3">
              @if(!empty($settings['facebook']))
                <a href="{{ $settings['facebook'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;" aria-label="Facebook SD Muhammadiyah Komplek Kolombo">
                  <svg width="19" height="19" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg>
                </a>
              @endif
              @if(!empty($settings['instagram']))
                <a href="{{ $settings['instagram'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;" aria-label="Instagram SD Muhammadiyah Komplek Kolombo">
                  <svg width="19" height="19" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg>
                </a>
              @endif
              @if(!empty($settings['tiktok']))
                <a href="{{ $settings['tiktok'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;" aria-label="TikTok SD Muhammadiyah Komplek Kolombo">
                  <svg width="19" height="19" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"/></svg>
                </a>
              @endif
              @if(!empty($settings['youtube']))
                <a href="{{ $settings['youtube'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;" aria-label="YouTube SD Muhammadiyah Komplek Kolombo">
                  <svg width="19" height="19" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"/></svg>
                </a>
              @endif
            </div>
          </div>
          
          <!-- Info Sekolah -->
          <div class="col-lg-3 col-md-6">
            <h5 class="fw-bold text-white mb-4">Info Sekolah</h5>
            <ul class="list-unstyled">
              <li class="mb-2"><a href="{{ route('home') }}" class="text-light text-decoration-none hover-white"><x-admin-icon name="chevron-right" size="14" class="text-warning me-2"/>Beranda</a></li>
              <li class="mb-2"><a href="{{ route('tentang') }}" class="text-light text-decoration-none hover-white"><x-admin-icon name="chevron-right" size="14" class="text-warning me-2"/>Profil Sekolah</a></li>
              <li class="mb-2"><a href="{{ route('prestasi') }}" class="text-light text-decoration-none hover-white"><x-admin-icon name="chevron-right" size="14" class="text-warning me-2"/>Prestasi Siswa</a></li>
              <li class="mb-2"><a href="{{ route('guru', ['tipe' => 'guru']) }}" class="text-light text-decoration-none hover-white"><x-admin-icon name="chevron-right" size="14" class="text-warning me-2"/>Struktural Pengajar</a></li>
              <li class="mb-2"><a href="{{ route('ekstrakurikuler') }}" class="text-light text-decoration-none hover-white"><x-admin-icon name="chevron-right" size="14" class="text-warning me-2"/>Ekstrakurikuler</a></li>
              <li class="mb-2"><a href="{{ route('berita') }}" class="text-light text-decoration-none hover-white"><x-admin-icon name="chevron-right" size="14" class="text-warning me-2"/>Papan Berita</a></li>
            </ul>
          </div>
          
          <!-- Lokasi Sekolah -->
          <div class="col-lg-5 col-md-12">
            <h5 class="fw-bold text-white mb-4">Lokasi Kami</h5>
            <div class="position-relative rounded-4 overflow-hidden" style="transform: translateZ(0); border-radius: 16px;">
              <!-- Overlay link to open Google Maps directly in new tab -->
              <a href="https://www.google.com/maps/search/?api=1&query=SD+Muhammadiyah+Komplek+Kolombo" target="_blank" class="position-absolute top-0 start-0 w-100 h-100 d-block" style="z-index: 10; background: rgba(0,0,0,0);" title="Buka di Google Maps" aria-label="Buka Peta Lokasi di Google Maps"></a>
              <iframe src="https://maps.google.com/maps?q=SD%20Muhammadiyah%20Komplek%20Kolombo&t=&z=17&ie=UTF8&iwloc=&output=embed" width="100%" height="200" style="border:0; border-radius: 16px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta Lokasi SD Muhammadiyah Komplek Kolombo"></iframe>
            </div>
          </div>
        </div>
      </div>
      <div class="container text-center">
        <p class="mb-0 text-light opacity-75">&copy; {{ date('Y') }} {{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Komplek Kolombo' }}. All rights reserved.</p>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Setup current date in Indonesian format inside Topbar
      document.addEventListener("DOMContentLoaded", function() {
        const topbarDateElement = document.getElementById('topbar-date');
        if (topbarDateElement) {
          const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
          const todayDate = new Date();
          // Translate English weekday to Indonesian if necessary, but locle 'id-ID' handles it
          topbarDateElement.innerHTML = todayDate.toLocaleDateString('id-ID', options);
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const revealTargets = document.querySelectorAll('main section:not(.hero-wrapper) > .container');

        if (!reduceMotion && 'IntersectionObserver' in window) {
          const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
              }
            });
          }, { threshold: 0.12, rootMargin: '0px 0px -45px' });

          revealTargets.forEach((target) => {
            target.classList.add('scroll-reveal');
            revealObserver.observe(target);
          });
        }

      });
    </script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <x-public-chatbot />
    @stack('scripts')
  </body>
</html>
