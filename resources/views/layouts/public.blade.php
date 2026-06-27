<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Kolombo' }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
      body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        color: #334155;
        padding-top: 110px; /* Offset for top-bar + navbar */
        font-size: 0.95rem; /* Slightly larger than 0.9rem for readability, but proportional */
        line-height: 1.6;
      }
      
      @media (max-width: 991.98px) {
        body { padding-top: 96px; }
      }
      
      @media (max-width: 767.98px) {
        body { padding-top: 86px; }
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

      .nav-link {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #0f172a !important;
        transition: color 0.3s;
      }

      .nav-link:hover, .nav-link.active {
        color: #172554 !important; /* Blue Primary */
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
             Selamat Datang di Website Resmi {{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Kolombo' }} | Terdepan Dalam Mendidik Generasi Islami, Cerdas, Berprestasi, dan Berkarakter Mulia!
          </marquee>
        </div>

        <!-- Kontak Cepat (Kanan) -->
        <div class="d-none d-md-flex align-items-center gap-2 ps-3 border-start border-white border-opacity-25" style="white-space: nowrap; font-size: 0.85rem; font-weight: 500; color: white;">
          <i class="bi bi-telephone-outbound text-warning me-1"></i> {{ $settings['telepon'] ?? '+62 274 1234567' }}
        </div>

      </div>
    </div>

    <nav class="navbar navbar-expand-lg fixed-top py-3">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
          @if(isset($settings['logo']) && $settings['logo'])
            <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" style="height: 40px; width: auto; object-fit: contain;">
          @else
            <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
          @endif
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
            <div class="d-flex mb-3">
              <i class="bi bi-geo-alt-fill text-warning me-3 fs-5"></i>
              <span class="text-light">{{ $settings['alamat'] ?? 'Jl. Kolombo No. 123, Yogyakarta' }}</span>
            </div>
            <div class="d-flex mb-3">
              <i class="bi bi-telephone-fill text-warning me-3 fs-5"></i>
              <span class="text-light">{{ $settings['telepon'] ?? '+62 274 1234567' }}</span>
            </div>
            <div class="d-flex mb-4">
              <i class="bi bi-envelope-fill text-warning me-3 fs-5"></i>
              <span class="text-light">{{ $settings['email'] ?? 'info@sdmuhkolombo.sch.id' }}</span>
            </div>
            <div class="d-flex gap-3">
              @if(!empty($settings['facebook'])) <a href="{{ $settings['facebook'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;"><i class="bi bi-facebook"></i></a> @endif
              @if(!empty($settings['instagram'])) <a href="{{ $settings['instagram'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;"><i class="bi bi-instagram"></i></a> @endif
              @if(!empty($settings['youtube'])) <a href="{{ $settings['youtube'] }}" target="_blank" class="text-white hover-white bg-white bg-opacity-10 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; text-decoration: none;"><i class="bi bi-youtube"></i></a> @endif
            </div>
          </div>
          
          <!-- Info Sekolah -->
          <div class="col-lg-3 col-md-6">
            <h5 class="fw-bold text-white mb-4">Info Sekolah</h5>
            <ul class="list-unstyled">
              <li class="mb-2"><a href="{{ route('home') }}" class="text-light text-decoration-none hover-white"><i class="bi bi-chevron-right text-warning small me-2"></i>Beranda</a></li>
              <li class="mb-2"><a href="{{ route('tentang') }}" class="text-light text-decoration-none hover-white"><i class="bi bi-chevron-right text-warning small me-2"></i>Profil Sekolah</a></li>
              <li class="mb-2"><a href="{{ route('prestasi') }}" class="text-light text-decoration-none hover-white"><i class="bi bi-chevron-right text-warning small me-2"></i>Prestasi Siswa</a></li>
              <li class="mb-2"><a href="{{ route('guru', ['tipe' => 'guru']) }}" class="text-light text-decoration-none hover-white"><i class="bi bi-chevron-right text-warning small me-2"></i>Struktural Pengajar</a></li>
              <li class="mb-2"><a href="{{ route('ekstrakurikuler') }}" class="text-light text-decoration-none hover-white"><i class="bi bi-chevron-right text-warning small me-2"></i>Ekstrakurikuler</a></li>
              <li class="mb-2"><a href="{{ route('berita') }}" class="text-light text-decoration-none hover-white"><i class="bi bi-chevron-right text-warning small me-2"></i>Papan Berita</a></li>
            </ul>
          </div>
          
          <!-- Lokasi Sekolah -->
          <div class="col-lg-5 col-md-12">
            <h5 class="fw-bold text-white mb-4">Lokasi Kami</h5>
            <div class="rounded overflow-hidden">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.0981966141443!2d110.3855523!3d-7.7794195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a59c0d30de0ad%3A0xe324dcd981d3f6d7!2sUniversitas%20Negeri%20Yogyakarta!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
      <div class="container text-center">
        <p class="mb-0 text-light opacity-75">&copy; {{ date('Y') }} {{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Kolombo' }}. All rights reserved.</p>
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
      });
    </script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @stack('scripts')
  </body>
</html>
