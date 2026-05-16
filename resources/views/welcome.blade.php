@extends('layouts.public')

@section('content')

    <!-- Hero Section -->
    <div class="position-relative bg-dark hero-wrapper" style="overflow: hidden;">
        <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner h-100">
                <!-- Slide 1 -->
                <div class="carousel-item active h-100">
                    @if(isset($settings['hero_image']) && $settings['hero_image'])
                        <img src="{{ asset('storage/' . $settings['hero_image']) }}" class="d-block w-100 h-100"
                            style="object-fit: cover; object-position: center;"
                            alt="{{ $settings['nama_sekolah'] ?? 'Sekolah' }}">
                    @else
                        <!-- Placeholder default jika tidak ada gambar hero -->
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2000&auto=format&fit=crop"
                            class="d-block w-100 h-100" style="object-fit: cover; object-position: center;"
                            alt="Fasilitas Sekolah">
                    @endif
                </div>
                <!-- Slide 2 -->
                <div class="carousel-item h-100">
                    <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?q=80&w=2000&auto=format&fit=crop"
                        class="d-block w-100 h-100" style="object-fit: cover; object-position: center;"
                        alt="Kegiatan Belajar">
                </div>
                <!-- Slide 3 -->
                <div class="carousel-item h-100">
                    <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=2000&auto=format&fit=crop"
                        class="d-block w-100 h-100" style="object-fit: cover; object-position: center;"
                        alt="Prestasi Siswa">
                </div>
            </div>
            <!-- Indicators / Bullets -->
            <div class="carousel-indicators mb-4">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"
                    aria-label="Slide 1" style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"
                    style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"
                    style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-dark {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.6) 50%, rgba(15, 23, 42, 0.1) 100%);
        }

        .tracking-wider {
            letter-spacing: 1px;
        }

        .section-title {
            position: relative;
            padding-bottom: 15px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background-color: #FEF102;
        }

        .hero-wrapper {
            height: 600px;
        }

        .sambutan-img-wrapper {
            min-height: 500px;
        }

        @media (max-width: 991.98px) {
            .hero-wrapper {
                height: 500px;
            }

            .sambutan-img-wrapper {
                min-height: 400px;
            }
        }

        @media (max-width: 767.98px) {
            .hero-wrapper {
                height: 400px;
            }

            .sambutan-img-wrapper {
                min-height: 350px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-wrapper {
                height: 300px;
            }

            .sambutan-img-wrapper {
                min-height: 250px;
            }
        }
    </style>

    <!-- Custom CSS for 5-Grid Sambutan & Guru -->
    <style>
        .seamless-grid-container {
            display: grid;
            grid-template-columns: 2fr 2.8fr 5.2fr;
            /* Proporsi: Foto Kepsek, Teks Sambutan (diperlebar), Swiper Guru */
            width: 100%;
            margin-bottom: 2rem;
            gap: 20px;
            /* Menambahkan jarak antar grid utama */
        }

        .seamless-grid-item {
            height: 380px;
            position: relative;
            border-radius: 8px;
            /* Menambahkan kelengkungan agar cantik saat dipisah */
            overflow: hidden;
            /* Mencegah gambar tumpah ke ujung border */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .seamless-grid-span-3 {
            min-width: 0;
            /* Mencegah swiper tumpah (overflow) dari layout flex/grid */
        }

        /* Base Card Styling */
        .guru-slide-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* Bayangan tiap card guru */
        }

        .guru-slide-img {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .guru-slide-img img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
        }

        /* Alternate background colors for images without pure backgrounds */
        .bg-red-custom {
            background-color: #dd1d1d;
        }

        .bg-blue-custom {
            background-color: #2e599e;
        }

        .guru-slide-info {
            background-color: #ffca28;
            padding: 1.2rem 1rem;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 1199.98px) {
            .seamless-grid-container {
                grid-template-columns: 2fr 3fr 5fr;
            }
        }

        @media (max-width: 991.98px) {
            .seamless-grid-container {
                grid-template-columns: 2fr 3fr;
                /* 2 kolom di tablet */
            }

            .seamless-grid-span-3 {
                grid-column: span 2;
                /* Full width */
            }
        }

        @media (max-width: 767.98px) {
            .seamless-grid-container {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .seamless-grid-item {
                height: 350px;
            }
        }
    </style>

    <style>
        .profil-grid-container {
            display: grid;
            grid-template-columns: 4.5fr 7.5fr;
            gap: 20px;
            width: 100%;
        }

        .profil-grid-item {
            min-height: 380px;
            border-radius: 8px;
            /* Rounded corners */
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* Soft shadow */
        }

        @media (max-width: 991.98px) {
            .profil-grid-container {
                grid-template-columns: 1fr;
            }

            .profil-grid-item {
                min-height: auto;
            }
        }
    </style>

    <!-- Section Tentang Sekolah (Sederhana & Proporsional) -->
    <section class="py-5 bg-white">
        <div class="container">
            
            <!-- Premium Welcome Header -->
            <div class="text-center mb-4 position-relative z-2">
                
                <!-- Main Title -->
                <h2 class="fw-bolder mb-3 lh-sm" style="font-size: clamp(1.75rem, 3vw, 2.25rem); letter-spacing: -0.5px; color: #0f172a;">
                    Selamat Datang di<br>
                    <span style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;">
                        {{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Kolombo' }}
                    </span>
                </h2>
                
                <!-- Underline Decoration -->
                <div class="d-flex justify-content-center align-items-center gap-2 mt-4">
                    <div style="height: 4px; width: 50px; background: linear-gradient(90deg, transparent, #1e3a8a); border-radius: 2px;"></div>
                    <div style="height: 8px; width: 8px; background-color: #FEF102; border-radius: 50%; box-shadow: 0 0 10px rgba(254, 241, 2, 0.5);"></div>
                    <div style="height: 4px; width: 50px; background: linear-gradient(270deg, transparent, #1e3a8a); border-radius: 2px;"></div>
                </div>
                
                <!-- Subtext -->
                <p class="text-secondary mt-4 mx-auto" style="max-width: 650px; font-size: 1rem; line-height: 1.7;">
                    Bersama membentuk generasi yang cerdas secara intelektual, unggul dalam berprestasi, dan berakhlak mulia berlandaskan nilai-nilai Islami.
                </p>
            </div>

                    </div>
    </section>

    <!-- Statistik Sekolah (Modern Geometric Cards) -->
    <section class="py-5 position-relative" style="background-color: #f8fafc;">
        <div class="container position-relative z-2">
            <div class="row g-4 justify-content-center">
                <!-- Stat 1 -->
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card border" style="border-color: rgba(0,135,78,0.15) !important;">
                        <div class="position-absolute top-0 end-0 p-2 opacity-10 transform-icon">
                            <i class="bi bi-person-badge-fill text-success" style="font-size: 6rem; transform: rotate(15deg);"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-badge-fill" style="font-size: 1.25rem;"></i>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1" style="font-size: 2.2rem; letter-spacing: -1px;">
                                {{ $countGuru }}<span class="text-warning" style="font-size: 1.2rem; vertical-align: middle;">+</span>
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider" style="font-size: 0.7rem;">Tenaga Pendidik</p>
                        </div>
                    </div>
                </div>
                <!-- Stat 2 -->
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card border" style="border-color: rgba(254,241,2,0.3) !important;">
                        <div class="position-absolute top-0 end-0 p-2 opacity-10 transform-icon">
                            <i class="bi bi-mortarboard-fill text-warning" style="font-size: 6rem; transform: rotate(-10deg);"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px;">
                                <i class="bi bi-mortarboard-fill" style="font-size: 1.25rem;"></i>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1" style="font-size: 2.2rem; letter-spacing: -1px;">
                                450<span class="text-success" style="font-size: 1.2rem; vertical-align: middle;">+</span>
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider" style="font-size: 0.7rem;">Peserta Didik</p>
                        </div>
                    </div>
                </div>
                <!-- Stat 3 -->
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card border" style="border-color: rgba(13,110,253,0.15) !important;">
                        <div class="position-absolute top-0 end-0 p-2 opacity-10 transform-icon">
                            <i class="bi bi-palette-fill text-primary" style="font-size: 6rem; transform: rotate(15deg);"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px;">
                                <i class="bi bi-palette-fill" style="font-size: 1.25rem;"></i>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1" style="font-size: 2.2rem; letter-spacing: -1px;">
                                {{ $countEkstra }}
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider" style="font-size: 0.7rem;">Kegiatan Ekstra</p>
                        </div>
                    </div>
                </div>
                <!-- Stat 4 -->
                <div class="col-6 col-md-3">
                    <div class="p-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card border" style="border-color: rgba(220,53,69,0.15) !important;">
                        <div class="position-absolute top-0 end-0 p-2 opacity-10 transform-icon">
                            <i class="bi bi-award-fill text-danger" style="font-size: 6rem; transform: rotate(-15deg);"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-danger bg-opacity-10 text-danger" style="width: 40px; height: 40px;">
                                <i class="bi bi-award-fill" style="font-size: 1.25rem;"></i>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1" style="font-size: 2.2rem; letter-spacing: -1px;">
                                {{ $countPrestasi }}<span class="text-warning" style="font-size: 1.2rem; vertical-align: middle;">+</span>
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider" style="font-size: 0.7rem;">Penghargaan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .z-2 { z-index: 2; }
        .tracking-wider { letter-spacing: 1.5px; }
        
        .stat-geometric-card {
            background-color: #ffffff;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .stat-geometric-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }
        
        .transform-icon i {
            display: inline-block;
            transition: all 0.5s ease;
        }
        
        .stat-geometric-card:hover .transform-icon i {
            transform: scale(1.1) rotate(0deg) !important;
            opacity: 0.7;
        }
    </style>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="profil-grid-container">
                <!-- Foto Tunggal (Kiri) -->
                <div class="profil-grid-item">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800&auto=format&fit=crop"
                        class="w-100 h-100" alt="Foto Fasilitas Sekolah" style="object-fit: cover;">
                </div>
                <!-- Teks Penuh (Kanan) -->
                <div class="profil-grid-item p-4 p-xl-5 d-flex flex-column justify-content-center"
                    style="background-color: #f8fafc; border: 1px solid rgba(0,0,0,0.05);">
                    <h6 class="text-uppercase fw-bold text-primary mb-2" style="font-size: 0.9rem; letter-spacing: 1px;">
                        Profil Singkat</h6>
                    <h3 class="fw-bold text-dark mb-3 lh-sm" style="font-size: 1.6rem;">Membentuk Generasi <span
                            class="text-primary">{{ $settings['beranda_profil_judul'] ?? 'Islami & Berprestasi' }}</span>
                    </h3>
                    <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 1rem;">
                        {{ $settings['beranda_profil_teks'] ?? ($settings['nama_sekolah'] ?? 'SD Muhammadiyah Kolombo' . ' hadir sebagai bangku pendidikan dasar yang mengintegrasikan kurikulum ilmu pengetahuan mutakhir dengan penanaman nilai-nilai adab dan akhlak Islam secara menyeluruh (holistik). Kami senantiasa berkomitmen untuk memberikan pembelajaran terbaik bagi tumbuh kembang putra-putri bangsa.') }}
                    </p>
                    <div>
                        <a href="{{ route('tentang') }}"
                            class="btn btn-outline-primary px-4 py-2 border-2 fw-bold shadow-sm transition-all hover-card text-uppercase"
                            style="font-size: 0.9rem; letter-spacing: 0.5px;">
                            Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    


    

    <!-- Sambutan & Daftar Guru Layour Kustom -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="seamless-grid-container">

                <!-- Grid 1: Foto Kepala Sekolah (Dikosongkan sesuai permintaan) -->
                <div class="seamless-grid-item">
                    <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                        <i class="bi bi-person text-secondary opacity-25" style="font-size: 6rem;"></i>
                    </div>
                </div>

                <!-- Grid 2: Sambutan Box -->
                <div class="seamless-grid-item p-3 p-xl-4 d-flex flex-column justify-content-center border-0"
                    style="background-color: #1e3a8a;">
                    <h4 class="text-white fw-bold mb-1 lh-sm" style="font-size: 1.1rem;">
                        {{ $settings['kepsek_nama'] ?? 'Drs. Ahmad Dahlan, M.Pd.' }}</h4>
                    <span class="text-warning mb-3 d-block" style="font-size: 0.85rem;">Kepala Sekolah</span>

                    <h6 class="text-white fw-bold text-uppercase mb-2" style="font-size: 0.9rem;">Kata Sambutan</h6>

                    <p class="text-white-50 small mb-4"
                        style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $settings['kepsek_sambutan'] ?? "Assalamu'alaikum warahmatullahi wabarakaatuh. Alhamdulillahirobbil 'aalamiin. Salam Bahagia... Kita panjatkan puji syukur ke hadirat Allah SWT, atas limpahan rahmat, taufik, hidayah, dan inayah-Nya." }}
                    </p>

                    <div>
                        <a href="{{ route('sambutan') }}"
                            class="btn btn-outline-warning rounded-0 px-3 py-1 border-2 fw-bold"
                            style="font-size: 0.8rem;">selengkapnya</a>
                    </div>
                </div>

                <!-- Grid 3, 4, 5: Swiper Guru -->
                <div class="seamless-grid-item seamless-grid-span-3">
                    <div class="swiper guruSwiper h-100 w-100 bg-light">
                        <div class="swiper-wrapper">
                            @forelse($gurus as $index => $guru)
                                <div class="swiper-slide">
                                    <div class="guru-slide-card">
                                        <div class="guru-slide-img {{ $index % 2 == 0 ? 'bg-red-custom' : 'bg-blue-custom' }}">
                                            @if($guru->foto)
                                                <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->nama }}">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute bg-dark bg-opacity-25">
                                                    <i class="bi bi-person text-white opacity-50 display-1"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="guru-slide-info">
                                            <p class="text-dark opacity-75 mb-1" style="font-size: 0.85rem;">
                                                {{ $guru->jabatan ?? 'Tenaga Pendidik' }}</p>
                                            <h6 class="text-dark fw-bold mb-0 lh-sm" style="font-size: 0.95rem;">
                                                {{ $guru->nama }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <!-- Placeholder jika data kosong -->
                                @for($i = 1; $i <= 4; $i++)
                                    <div class="swiper-slide">
                                        <div class="guru-slide-card">
                                            <div class="guru-slide-img {{ $i % 2 == 0 ? 'bg-red-custom' : 'bg-blue-custom' }}">
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute">
                                                    <i class="bi bi-person text-white opacity-50 display-1"></i>
                                                </div>
                                            </div>
                                            <div class="guru-slide-info">
                                                <p class="text-dark opacity-75 mb-1" style="font-size: 0.85rem;">Jabatan</p>
                                                <h6 class="text-dark fw-bold mb-0 lh-sm" style="font-size: 0.95rem;">Nama Staf/Guru
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Agenda, Berita & Artikel Kombinasi -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-5">

                <!-- Kolom Ekstrakurikuler -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <h3 class="section-title fw-bold mb-0" style="font-size: 1.5rem;">Ekstrakurikuler</h3>
                        <a href="{{ route('ekstrakurikuler') }}" class="btn btn-outline-primary rounded-1 btn-sm fw-bold">Lihat Semua</a>
                    </div>
                    <div class="row g-4">
                        @forelse($ekstrakurikulers as $ekstra)
                            <div class="col-md-6">
                                <div class="card h-100 border-0 rounded-3 shadow-sm overflow-hidden group-hover">
                                    <div class="position-relative" style="height: 200px;">
                                        @if($ekstra->foto)
                                            <img src="{{ asset('storage/' . $ekstra->foto) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $ekstra->nama }}">
                                        @else
                                            <div class="w-100 h-100 bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-activity text-secondary fs-1 opacity-50"></i>
                                            </div>
                                        @endif
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="badge bg-primary p-2 fs-7 rounded-1 shadow-sm"><i class="bi bi-star-fill text-warning me-1"></i> Program Unggulan</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <h4 class="card-title fw-bold mb-3 lh-base" style="font-size: 1.15rem;">
                                            <a href="{{ route('ekstrakurikuler') }}" class="text-dark text-decoration-none hover-primary">{{ Str::limit($ekstra->nama, 60) }}</a>
                                        </h4>
                                        <p class="card-text text-secondary mb-0" style="font-size: 0.95rem;">
                                            {{ Str::limit(strip_tags($ekstra->deskripsi), 80) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 py-5 text-center text-muted">Belum ada program ekstrakurikuler.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Kolom Berita Terkini -->
                <div class="col-lg-4">
                    <h3 class="section-title fw-bold mb-4" style="font-size: 1.5rem;">Berita Terkini</h3>
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-0">
                            @forelse($beritas as $berita)
                                <div class="d-flex align-items-center p-4 border-bottom position-relative hover-agenda transition-all">
                                    <!-- Tanggal Badge Boxy -->
                                    <div class="bg-primary text-white text-center rounded-2 me-3 shadow-sm flex-shrink-0" style="min-width: 65px; overflow: hidden;">
                                        <div class="bg-dark fw-bold small py-1" style="font-size: 0.70rem;">
                                            {{ strtoupper(\Carbon\Carbon::parse($berita->tanggal)->translatedFormat('M')) }}
                                        </div>
                                        <div class="fw-bold py-2 lh-1" style="font-size: 1.4rem;">
                                            {{ \Carbon\Carbon::parse($berita->tanggal)->format('d') }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1 lh-sm" style="font-size: 0.95rem;">
                                            <a href="{{ route('berita.detail', $berita->id) }}" class="text-dark text-decoration-none hover-primary">
                                                {{ Str::limit($berita->judul, 50) }}
                                            </a>
                                        </h6>
                                        <p class="text-secondary small mb-0"><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($berita->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center text-muted">Belum ada berita terbaru.</div>
                            @endforelse
                        </div>
                        <div class="card-footer bg-white border-0 text-center p-3">
                            <a href="{{ route('berita') }}" class="text-decoration-none fw-bold text-primary">Lihat Papan Berita <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Prestasi Highlight -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div class="section-title mb-0" style="font-size: 1.4rem;">Prestasi Siswa</div>
                <a href="{{ route('prestasi') }}" class="btn btn-outline-primary rounded-1 btn-sm fw-bold">Lihat Semua</a>
            </div>
            <div class="row g-4">
                @forelse($prestasis as $prestasi)
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-white shadow-sm border-0 h-100 group-hover rounded-4">
                            <div class="position-relative rounded-top-4 overflow-hidden mb-3" style="height: 200px;">
                                @if($prestasi->foto)
                                    <img src="{{ asset('storage/' . $prestasi->foto) }}" class="w-100 h-100"
                                        style="object-fit: cover;" alt="{{ $prestasi->judul }}">
                                @else
                                    <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-trophy text-secondary fs-1 opacity-50"></i>
                                    </div>
                                @endif
                                <div
                                    class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center opacity-0 hover-opacity-100 transition-all">
                                    <span
                                        class="btn btn-warning rounded-pill btn-sm fw-bold border-0">{{ \Carbon\Carbon::parse($prestasi->tanggal)->format('Y') }}</span>
                                </div>
                            </div>
                            <div class="p-3 pt-0">
                                <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.1rem;">{{ $prestasi->judul }}</h5>
                                <p class="text-secondary small mb-0">{{ Str::limit($prestasi->deskripsi, 50) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center text-muted">
                        <i class="bi bi-trophy display-4 mb-3 d-block opacity-25"></i>
                        Belum ada data prestasi yang ditambahkan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        .hover-primary:hover {
            color: #1e3a8a !important;
        }

        .hover-agenda:hover {
            background-color: #f8fafc;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }

        .opacity-0 {
            opacity: 0;
        }

        .hover-opacity-100 {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .group-hover:hover .hover-opacity-100 {
            opacity: 1;
        }
    </style>

    <!-- Masukan & Saran (Ultra Clean) -->
    <section id="kontak" class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-between align-items-center g-4">
                <div class="col-lg-4">
                    <h6 class="text-primary fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.9rem;">Hubungi
                        Kami</h6>
                    <h3 class="fw-bold text-dark mb-3 lh-sm" style="font-size: 1.6rem; letter-spacing: -0.5px;">Suara
                        Anda<br>Sangat Berarti.</h3>
                    <p class="text-secondary mb-5" style="line-height: 1.7; font-size: 1rem;">
                        Kritik, saran, maupun masukan Anda sangat membantu kami untuk terus berkembang memberikan layanan
                        pendidikan terbaik.
                    </p>

                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-start gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Alamat Sekolah</h6>
                                <p class="text-secondary mb-0 small">
                                    {{ $settings['alamat'] ?? 'Jl. Kolombo No. 123, Yogyakarta' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Layanan Telepon</h6>
                                <p class="text-secondary mb-0 small">{{ $settings['telepon'] ?? '+62 274 1234567' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Dukungan Email</h6>
                                <p class="text-secondary mb-0 small">{{ $settings['email'] ?? 'info@sdmuhkolombo.sch.id' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="bg-white p-4 p-md-5 rounded-4 border border-opacity-10 shadow-sm">
                        <h4 class="fw-bold mb-4 text-dark">Tulis Pesan</h4>

                        @if(session('success_pesan'))
                            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                                {{ session('success_pesan') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('pesan.store') }}" method="POST">
                            @csrf
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light border-0 shadow-none" id="nama"
                                            name="nama" placeholder="Nama Anda">
                                        <label for="nama" class="text-muted">Nama Anda (Opsional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control bg-light border-0 shadow-none" id="email"
                                            name="email" placeholder="Alamat Email">
                                        <label for="email" class="text-muted">Email (Opsional)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-4">
                                <textarea class="form-control bg-light border-0 shadow-none" id="pesan" name="pesan"
                                    placeholder="Saran & Masukan" style="height: 160px; resize: none;" required></textarea>
                                <label for="pesan" class="text-muted">Pesan, kritik, atau saran... <span
                                        class="text-danger">*</span></label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-2">
                                <p class="text-secondary small mb-0 w-md-50" style="line-height: 1.5;">
                                    Privasi aman. Kosongkan identitas di atas mengirim secara anonim.
                                </p>
                                <button type="submit"
                                    class="btn btn-primary px-5 py-3 rounded-pill fw-bold text-uppercase tracking-wider"
                                    style="font-size: 0.9rem;">
                                    Bagikan Pesan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var guruSwiper = new Swiper(".guruSwiper", {
                    slidesPerView: 1,
                    spaceBetween: 20, /* Jeda antar card guru disamakan dengan gap CSS grid */
                    loop: true,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 2,
                        },
                        1200: {
                            slidesPerView: 3,
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection
