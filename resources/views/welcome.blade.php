{{--
    Halaman welcome.blade.php (Beranda Utama / Landing Page Publik)
    Menampilkan slider hero kustom, informasi profil singkat sekolah, statistik peserta didik & pendidik,
    kata sambutan kepala sekolah, profil guru & staf (slider swiper), daftar ekstrakurikuler, berita terbaru,
    prestasi siswa, dan formulir pengiriman kritik & saran kontak (anonim/non-anonim).
--}}
@extends('layouts.public')

@section('content')

    <!-- Hero Section -->
    @php
        $heroSlides = [];
        
        // Cek slide kustom 1
        if (isset($settings['hero_image']) && $settings['hero_image'] && file_exists(public_path('storage/' . $settings['hero_image']))) {
            $heroSlides[] = [
                'url' => asset('storage/' . $settings['hero_image']),
                'alt' => $settings['nama_sekolah'] ?? 'Sekolah'
            ];
        }
        
        // Dukungan untuk slide kustom tambahan di masa mendatang
        if (isset($settings['hero_image_2']) && $settings['hero_image_2'] && file_exists(public_path('storage/' . $settings['hero_image_2']))) {
            $heroSlides[] = [
                'url' => asset('storage/' . $settings['hero_image_2']),
                'alt' => 'Slide 2'
            ];
        }
        if (isset($settings['hero_image_3']) && $settings['hero_image_3'] && file_exists(public_path('storage/' . $settings['hero_image_3']))) {
            $heroSlides[] = [
                'url' => asset('storage/' . $settings['hero_image_3']),
                'alt' => 'Slide 3'
            ];
        }

        // Jika tidak ada gambar kustom, gunakan latar brand lokal tanpa ketergantungan internet
        if (empty($heroSlides)) {
            $heroSlides[] = [
                'url' => null,
                'alt' => 'Latar sekolah'
            ];
        }
    @endphp

    <div class="position-relative bg-dark hero-wrapper" style="overflow: hidden;">
        @if(count($heroSlides) > 1)
            <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner h-100">
                    @foreach($heroSlides as $index => $slide)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-100">
                            <img src="{{ $slide['url'] }}" class="d-block w-100 h-100"
                                style="object-fit: cover; object-position: center;"
                                alt="{{ $slide['alt'] }}">
                        </div>
                    @endforeach
                </div>
                <!-- Indicators / Bullets (Hanya muncul jika lebih dari 1 gambar) -->
                <div class="carousel-indicators mb-4">
                    @foreach($heroSlides as $index => $slide)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}" 
                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $index + 1 }}" 
                            style="width: 12px; height: 12px; border-radius: 50%; margin: 0 6px;"></button>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Tampilan Static Banner jika hanya 1 gambar (tanpa indikator & tanpa slide berpindah) -->
            <div class="h-100 w-100">
                @if($heroSlides[0]['url'])
                    <img src="{{ $heroSlides[0]['url'] }}" class="d-block w-100 h-100"
                        style="object-fit: cover; object-position: center;"
                        alt="{{ $heroSlides[0]['alt'] }}">
                @else
                    <div class="w-100 h-100 border-bottom" style="background: #f1f3f5; border-color: #e2e8f0 !important;">
                        <img src="{{ asset('assets/images/no-image-available.jpg') }}" class="d-block w-100 h-100" style="object-fit: contain; object-position: center; mix-blend-mode: multiply;" alt="Gambar tidak tersedia">
                    </div>
                @endif
            </div>
        @endif

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

        .hero-default-bg {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 15% 25%, rgba(254, 241, 2, .14) 0 2px, transparent 3px),
                radial-gradient(circle at 85% 75%, rgba(255, 255, 255, .10) 0 2px, transparent 3px),
                linear-gradient(135deg, #172554 0%, #1e3a8a 58%, #2563eb 100%);
            background-size: 48px 48px, 64px 64px, 100% 100%;
        }

        .hero-default-bg::after {
            content: "";
            position: absolute;
            right: -8%;
            bottom: -45%;
            width: 55%;
            aspect-ratio: 1;
            border: 2px solid rgba(254, 241, 2, .15);
            border-radius: 50%;
            box-shadow:
                0 0 0 70px rgba(255, 255, 255, .035),
                0 0 0 140px rgba(254, 241, 2, .025);
        }

        .welcome-poster-section {
            position: relative;
            overflow: hidden;
            padding: 5.5rem 0;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        .welcome-image-container {
            position: relative;
            padding-right: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .welcome-image-container[data-tilt] {
            transform-style: preserve-3d;
            transform-origin: center;
            transition: transform 180ms ease-out;
            will-change: transform;
        }

        .welcome-image-accent {
            position: absolute;
            top: -15px;
            left: -15px;
            width: 90px;
            height: 90px;
            border-top: 5px solid #FEF102;
            border-left: 5px solid #FEF102;
            z-index: 1;
            border-radius: 12px 0 0 0;
        }

        .welcome-img-wrapper {
            position: relative;
            z-index: 2;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            height: 420px;
        }

        .welcome-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .welcome-image-container:hover .welcome-img-wrapper img {
            transform: scale(1.03);
        }

        .welcome-floating-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 3;
            background: #ffffff;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            border: 1px solid #f1f5f9;
            min-width: 210px;
            animation: welcomeBadgeFloat 3s infinite alternate ease-in-out;
        }

        @keyframes welcomeBadgeFloat {
            0% { transform: translateY(0); }
            100% { transform: translateY(-8px); }
        }

        .badge-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .welcome-text-container {
            position: relative;
            z-index: 5;
        }

        .welcome-pre-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .welcome-headline {
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            font-weight: 850;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.75px;
        }

        .text-underline-highlight {
            position: relative;
            display: inline-block;
            z-index: 1;
        }

        .text-underline-highlight::after {
            content: "";
            position: absolute;
            bottom: 4px;
            left: 0;
            width: 100%;
            height: 12px;
            background: #FEF102;
            z-index: -1;
            opacity: 0.9;
            border-radius: 2px;
        }

        .welcome-desc {
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .welcome-desc strong {
            color: #1e3a8a;
            font-weight: 700;
        }

        .welcome-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.2s ease;
        }

        .welcome-feature-item:hover {
            transform: translateX(4px);
        }

        .welcome-feature-icon {
            font-size: 1.25rem;
            color: #16a34a;
            flex-shrink: 0;
        }

        .welcome-feature-text {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .welcome-btn-primary {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            transition: all 0.3s ease;
        }

        .welcome-btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(29, 78, 216, 0.15);
        }

        @media (max-width: 991.98px) {
            .welcome-image-container {
                padding-right: 0;
                padding-bottom: 0;
                margin-bottom: 3rem;
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }
            .welcome-img-wrapper {
                height: 340px;
            }
            .welcome-poster-section {
                padding: 4.5rem 0;
            }
        }

        @media (prefers-reduced-motion: reduce), (hover: none), (pointer: coarse) {
            .welcome-image-container[data-tilt] {
                transform: none !important;
                transition: none;
            }
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

            .welcome-poster-section {
                padding: 3rem 0;
            }

            .welcome-school-name {
                padding: .7rem 1rem;
            }

            .sambutan-img-wrapper {
                min-height: 250px;
            }
        }

        .home-news-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .home-news-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .75rem 1.5rem rgba(15, 23, 42, .12) !important;
        }

        .home-news-main-image {
            height: 360px;
            object-fit: cover;
        }

        .home-news-side-image {
            width: 150px;
            min-height: 145px;
            object-fit: cover;
        }

        @media (max-width: 767.98px) {
            .home-news-main-image {
                height: 240px;
            }

            .home-news-side-image {
                width: 120px;
                min-height: 130px;
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

        .profil-grid-item[data-parallax] img {
            transform: translate3d(0, var(--profil-parallax-y, 0), 0) scale(1.06);
            transition: transform 120ms linear;
            will-change: transform;
        }

        @media (prefers-reduced-motion: reduce) {
            .profil-grid-item[data-parallax] img {
                transform: none;
                transition: none;
            }
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

    <!-- Selamat Datang (Elegant Premium Split Layout) -->
    <section class="welcome-poster-section">
        <div class="container">
            <div class="row align-items-center">
                
                <!-- Left Column: Visual Image Composition -->
                <div class="col-lg-6">
                    <div class="welcome-image-container" data-tilt>
                        <div class="welcome-image-accent"></div>
                        <div class="welcome-img-wrapper">
                            @if(isset($settings['welcome_image']) && $settings['welcome_image'] && file_exists(public_path('storage/' . $settings['welcome_image'])))
                                <img src="{{ asset('storage/' . $settings['welcome_image']) }}" alt="SD Muhammadiyah Komplek Kolombo Yogyakarta">
                            @else
                                <img src="{{ asset('assets/images/no-image-available.jpg') }}" class="w-100 h-100" style="object-fit: contain; object-position: center; transform: none;" alt="Gambar tidak tersedia">
                            @endif
                        </div>
                        <div class="welcome-floating-badge">
                            <div class="d-flex align-items-center gap-3">
                                <div class="badge-icon-box bg-warning bg-opacity-10 text-warning">
                                    <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                        <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708"/>
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Akreditasi A</h6>
                                    <p class="text-secondary mb-0 small" style="font-size: 0.75rem;">Unggul & Terpercaya</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Clean Premium Welcome Content -->
                <div class="col-lg-6">
                    <div class="welcome-text-container ps-lg-4">
                        <span class="welcome-pre-title d-block mb-2">Selamat Datang</span>
                        <h2 class="welcome-headline mb-3">
                            Membina Generasi Islami, <br>
                            <span class="text-underline-highlight">Cerdas & Berkarakter</span>
                        </h2>
                        
                        <p class="welcome-desc mb-4">
                            Selamat datang di portal resmi <strong>{{ $settings['nama_sekolah'] ?? 'SD Muhammadiyah Komplek Kolombo' }}</strong>. Kami berkomitmen menyelenggarakan pendidikan dasar holistik yang memadukan keunggulan akademis dengan penanaman nilai-nilai adab dan akhlak mulia sejak dini.
                        </p>
                        
                        <!-- Value Features -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="welcome-feature-item">
                                    <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor" class="welcome-feature-icon" aria-hidden="true">
                                        <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                                        <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                    </svg>
                                    <span class="welcome-feature-text">Kurikulum Terpadu</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="welcome-feature-item">
                                    <x-admin-icon name="people" size="24" class="welcome-feature-icon"/>
                                    <span class="welcome-feature-text">Pengajar Profesional</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="welcome-feature-item">
                                    <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor" class="welcome-feature-icon" aria-hidden="true">
                                        <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z"/>
                                    </svg>
                                    <span class="welcome-feature-text">Bakat & Minat Anak</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="welcome-feature-item">
                                    <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor" class="welcome-feature-icon" aria-hidden="true">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                    </svg>
                                    <span class="welcome-feature-text">Fasilitas Lengkap</span>
                                </div>
                            </div>
                        </div>
                        

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Statistik Sekolah (Modern Geometric Cards) -->
    <section class="py-5 position-relative" style="background-color: #f8fafc;">
        <div class="container position-relative z-2">
            <div class="row g-4 justify-content-center">
                <!-- Stat 1 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 p-lg-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card">
                        <div class="transform-icon">
                            <svg width="88" height="88" viewBox="0 0 16 16" fill="currentColor" class="text-success" style="transform: rotate(15deg);" aria-hidden="true">
                                <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z"/>
                            </svg>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px;">
                                <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                    <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z"/>
                                </svg>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1 stat-value">
                                {{ $countTenagaPendidik }}<span class="text-warning" style="font-size: 1.2rem; vertical-align: middle;">+</span>
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider" style="font-size: 0.7rem;">Tenaga Pendidik</p>
                        </div>
                    </div>
                </div>
                <!-- Stat 2 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 p-lg-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card">
                        <div class="transform-icon">
                            <x-admin-icon name="people" size="88" class="text-warning" style="transform: rotate(-10deg);"/>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px;">
                                <x-admin-icon name="people" size="22"/>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1 stat-value">
                                {{ $countPesertaDidik }}<span class="text-success" style="font-size: 1.2rem; vertical-align: middle;">+</span>
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider"
                               style="font-size: 0.7rem;">Peserta Didik</p>
                        </div>
                    </div>
                </div>
                <!-- Stat 3 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 p-lg-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card">
                        <div class="transform-icon">
                            <x-admin-icon name="ekstrakurikuler" size="88" class="stat-extra-icon" style="transform: rotate(15deg);"/>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2" style="width: 40px; height: 40px; color: #7c3aed; background-color: rgba(124,58,237,0.10);">
                                <x-admin-icon name="ekstrakurikuler" size="22" class="stat-extra-icon"/>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1 stat-value">
                                {{ $countEkstra }}
                            </h2>
                            <p class="text-secondary fw-bold text-uppercase mb-0 tracking-wider"
                               style="font-size: 0.7rem;">Kegiatan Ekstra</p>
                        </div>
                    </div>
                </div>
                <!-- Stat 4 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 p-lg-4 rounded-4 shadow-sm h-100 position-relative overflow-hidden stat-geometric-card">
                        <div class="transform-icon">
                            <x-admin-icon name="award" size="88" class="text-danger stat-award-icon" style="transform: rotate(-15deg);"/>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-2 bg-danger bg-opacity-10 text-danger" style="width: 40px; height: 40px;">
                                <x-admin-icon name="award" size="22" class="stat-award-icon"/>
                            </div>
                            <h2 class="fw-bolder text-dark mb-1 stat-value">
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

        .stat-value {
            font-size: 2.2rem;
            letter-spacing: -1px;
        }

        .transform-icon {
            position: absolute;
            bottom: -15px;
            right: -15px;
            opacity: 0.06 !important;
            pointer-events: none;
            z-index: 0;
            transition: opacity 0.4s ease;
        }

        .transform-icon svg,
        .transform-icon img {
            display: inline-block;
            transition: transform 0.5s ease;
        }

        .stat-award-icon {
            filter: invert(29%) sepia(79%) saturate(2150%) hue-rotate(334deg) brightness(91%) contrast(89%);
        }

        .stat-extra-icon {
            filter: invert(28%) sepia(81%) saturate(2364%) hue-rotate(252deg) brightness(91%) contrast(91%);
        }
        
        .stat-geometric-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }
        
        .stat-geometric-card:hover .transform-icon {
            opacity: 0.15 !important;
        }
        
        .stat-geometric-card:hover .transform-icon svg,
        .stat-geometric-card:hover .transform-icon img {
            transform: scale(1.1) rotate(0deg) !important;
        }

        @media (max-width: 575.98px) {
            .stat-geometric-card {
                min-height: 120px;
            }

            .stat-value {
                font-size: 1.9rem;
            }

            .transform-icon {
                bottom: -10px;
                right: -10px;
            }

            .transform-icon svg,
            .transform-icon img {
                width: 4rem;
                height: 4rem;
            }
        }
    </style>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="profil-grid-container">
                <!-- Foto Tunggal (Kiri) -->
                <div class="profil-grid-item" data-parallax>
                    @if(isset($tentang) && $tentang->gambar && file_exists(public_path('storage/' . $tentang->gambar)))
                        <img src="{{ asset('storage/' . $tentang->gambar) }}" class="w-100 h-100" alt="Foto Tentang Sekolah" style="object-fit: cover;">
                    @else
                        <img src="{{ asset('assets/images/no-image-available.jpg') }}" class="w-100 h-100 border" style="object-fit: cover; min-height: 380px;" alt="Gambar tidak tersedia">
                    @endif
                </div>
                <!-- Teks Penuh (Kanan) -->
                <div class="profil-grid-item p-4 p-xl-5 d-flex flex-column justify-content-center"
                    style="background-color: #f8fafc; border: 1px solid rgba(0,0,0,0.05);">
                    <h6 class="text-uppercase fw-bold text-primary mb-2" style="font-size: 0.9rem; letter-spacing: 1px;">
                        Tentang Sekolah</h6>
                    <h3 class="fw-bold text-dark mb-3 lh-sm" style="font-size: 1.6rem;">Membentuk Generasi <span
                            class="text-primary">{{ optional($tentang)->judul ?? 'Islami & Berprestasi' }}</span>
                    </h3>
                    <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 1rem;">
                        {{ Str::limit(strip_tags(optional($tentang)->konten ?? 'SD Muhammadiyah Komplek Kolombo hadir sebagai lembaga pendidikan dasar Islam yang mengintegrasikan kurikulum ilmu pengetahuan umum dengan penanaman nilai-nilai adab dan akhlak Islam secara menyeluruh (holistik). Kami senantiasa berkomitmen untuk memberikan pembelajaran terbaik guna mendukung tumbuh kembang rohani, jasmani, dan intelektual putra-putri bangsa agar siap menghadapi tantangan zaman.'), 220) }}
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
    <section class="py-5" style="background-color: #f8fafc;">
        <div class="container">
            <div class="seamless-grid-container">

                <!-- Grid 1: Foto Kepala Sekolah -->
                <div class="seamless-grid-item">
                    @if(isset($sambutan) && $sambutan->gambar && file_exists(public_path('storage/' . $sambutan->gambar)))
                        <img src="{{ asset('storage/' . $sambutan->gambar) }}" class="w-100 h-100" 
                            style="object-fit: cover; object-position: top;" 
                            alt="Foto {{ optional($sambutan)->judul ?? 'Kepala Sekolah' }}">
                    @else
                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                            <svg width="96" height="96" viewBox="0 0 16 16" fill="currentColor" class="text-secondary opacity-25" aria-hidden="true">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Grid 2: Sambutan Box -->
                <div class="seamless-grid-item p-3 p-xl-4 d-flex flex-column justify-content-center border-0"
                    style="background-color: #1e3a8a;">
                    <h4 class="text-white fw-bold mb-1 lh-sm" style="font-size: 1.1rem;">
                        {{ optional($sambutan)->judul ?? 'Drs. Ahmad Dahlan, M.Pd.' }}</h4>
                    <span class="text-warning mb-3 d-block" style="font-size: 0.85rem;">Kepala Sekolah</span>

                    <h6 class="text-white fw-bold text-uppercase mb-2" style="font-size: 0.9rem;">Kata Sambutan</h6>

                    <p class="text-white-50 small mb-4"
                        style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ strip_tags(optional($sambutan)->konten ?? "Assalamu'alaikum warahmatullahi wabarakaatuh. Alhamdulillahirobbil 'aalamiin. Salam Bahagia... Kita panjatkan puji syukur ke hadirat Allah SWT, atas limpahan rahmat, taufik, hidayah, dan inayah-Nya.") }}
                    </p>

                    <div>
                        <a href="{{ route('sambutan') }}"
                            class="btn btn-outline-warning rounded-0 px-3 py-1 border-2 fw-bold"
                            style="font-size: 0.8rem;">selengkapnya</a>
                    </div>
                </div>

                <!-- Grid 3, 4, 5: Guru dan staf dari menu Struktural -->
                <div class="seamless-grid-item seamless-grid-span-3">
                    <div class="swiper guruSwiper h-100 w-100 bg-light">
                        <div class="swiper-wrapper">
                            @forelse($tenagaPendidik as $tenaga)
                                <div class="swiper-slide">
                                    <div class="guru-slide-card">
                                        <div class="guru-slide-img {{ $loop->iteration % 2 === 1 ? 'bg-blue-custom' : 'bg-red-custom' }}">
                                            @if($tenaga->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenaga->foto))
                                                <img src="{{ asset('storage/' . $tenaga->foto) }}" alt="{{ $tenaga->nama }}">
                                            @else
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute">
                                                    <svg width="80" height="80" viewBox="0 0 16 16" fill="currentColor" class="text-white opacity-50" aria-hidden="true">
                                                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="guru-slide-info">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge {{ $tenaga->tipe === 'guru' ? 'bg-primary' : 'bg-secondary' }} text-uppercase"
                                                      style="font-size: 0.6rem;">
                                                    {{ $tenaga->tipe === 'guru' ? 'Guru' : 'Staf' }}
                                                </span>
                                                <span class="text-dark opacity-75 text-truncate" style="font-size: 0.8rem;">
                                                    {{ $tenaga->jabatan ?? 'Tenaga Pendidik' }}
                                                </span>
                                            </div>
                                            <h6 class="text-dark fw-bold mb-0 lh-sm" style="font-size: 0.95rem;">
                                                {{ $tenaga->nama }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <!-- Placeholder jika data kosong -->
                                @for($i = 1; $i <= 6; $i++)
                                    <div class="swiper-slide">
                                        <div class="guru-slide-card">
                                            <div class="guru-slide-img {{ $i % 2 === 1 ? 'bg-blue-custom' : 'bg-red-custom' }}">
                                                <div
                                                    class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute">
                                                    <svg width="80" height="80" viewBox="0 0 16 16" fill="currentColor" class="text-white opacity-50" aria-hidden="true">
                                                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="guru-slide-info">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge {{ $i % 2 === 1 ? 'bg-primary' : 'bg-secondary' }} text-uppercase" style="font-size: 0.6rem;">
                                                        {{ $i % 2 === 1 ? 'Guru' : 'Staf' }}
                                                    </span>
                                                    <span class="text-dark opacity-75 text-truncate" style="font-size: 0.8rem;">
                                                        {{ $i % 2 === 1 ? 'Jabatan' : 'Staf Sekolah' }}
                                                    </span>
                                                </div>
                                                <h6 class="text-dark fw-bold mb-0 lh-sm" style="font-size: 0.95rem;">
                                                    {{ $i % 2 === 1 ? 'Nama Guru' : 'Nama Staf' }}
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

    <!-- Program Ekstrakurikuler -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <h3 class="section-title fw-bold mb-0" style="font-size: 1.5rem;">Ekstrakurikuler</h3>
                <a href="{{ route('ekstrakurikuler') }}" class="btn btn-outline-primary rounded-1 btn-sm fw-bold">Lihat Semua</a>
            </div>
            <div class="row g-4">
                @forelse($ekstrakurikulers as $ekstra)
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 border-0 rounded-3 shadow-sm overflow-hidden group-hover">
                            <div class="position-relative" style="height: 200px;">
                                @if($ekstra->foto)
                                    <img src="{{ asset('storage/' . $ekstra->foto) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $ekstra->nama }}">
                                @else
                                    <div class="w-100 h-100 bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center">
                                        <x-admin-icon name="ekstrakurikuler" size="48" class="text-secondary opacity-50"/>
                                    </div>
                                @endif
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
                    <div class="col-12">
                        <div class="home-empty-state">
                            <div class="home-empty-state-icon">
                                <x-admin-icon name="ekstrakurikuler" size="48"/>
                            </div>
                            <h4>Belum Ada Kegiatan</h4>
                            <p>Program ekstrakurikuler akan ditampilkan di sini.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Berita Terkini -->
    <section class="py-5" style="background-color: #f8fafc;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="section-title fw-bold mb-2" style="font-size: 1.5rem;">Berita Terkini</h3>
                    <p class="text-secondary mb-0">Informasi dan kegiatan terbaru dari sekolah</p>
                </div>
                <a href="{{ route('berita') }}" class="btn btn-outline-primary rounded-1 btn-sm fw-bold d-none d-sm-inline-flex align-items-center">
                    Lihat Semua
                </a>
            </div>

            @php
                $beritaUtama = $beritas->first();
                $beritaPendamping = $beritas->skip(1);
            @endphp

            @if($beritaUtama)
                <div class="row g-4">
                    <div class="col-lg-7">
                        <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden home-news-card">
                            @if($beritaUtama->gambar)
                                <img src="{{ asset('storage/' . $beritaUtama->gambar) }}"
                                     class="card-img-top home-news-main-image"
                                     alt="{{ $beritaUtama->judul }}">
                            @else
                                <div class="home-news-main-image bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                    <x-admin-icon name="news" size="80" class="text-secondary opacity-50"/>
                                </div>
                            @endif
                            <div class="card-body p-4 p-lg-5 position-relative">
                                <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small mb-3">
                                    <span class="badge bg-primary rounded-pill px-3 py-2">Berita Utama</span>
                                    <span><x-admin-icon name="classes" size="14" class="me-1"/>{{ \Carbon\Carbon::parse($beritaUtama->tanggal)->translatedFormat('d F Y') }}</span>
                                </div>
                                <h4 class="fw-bold text-dark lh-sm mb-4">
                                    <a href="{{ route('berita.detail', $beritaUtama->id) }}" class="text-dark text-decoration-none stretched-link">
                                        {{ Str::limit($beritaUtama->judul, 90) }}
                                    </a>
                                </h4>
                                <p class="text-secondary mb-0">{{ Str::limit(strip_tags($beritaUtama->isi), 170) }}</p>
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-3 h-100">
                            @foreach($beritaPendamping as $berita)
                                <article class="card border-0 shadow-sm rounded-4 overflow-hidden home-news-card flex-grow-1">
                                    <div class="d-flex h-100 position-relative">
                                        @if($berita->gambar)
                                            <img src="{{ asset('storage/' . $berita->gambar) }}"
                                                 class="home-news-side-image flex-shrink-0"
                                                 alt="{{ $berita->judul }}">
                                        @else
                                            <div class="home-news-side-image flex-shrink-0 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                                <x-admin-icon name="news" size="42" class="text-secondary opacity-50"/>
                                            </div>
                                        @endif
                                        <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-center">
                                            <p class="text-secondary small mb-2">
                                                <x-admin-icon name="classes" size="14" class="me-1"/>{{ \Carbon\Carbon::parse($berita->tanggal)->translatedFormat('d M Y') }}
                                            </p>
                                            <h5 class="fw-bold lh-sm mb-2" style="font-size: 1rem;">
                                                <a href="{{ route('berita.detail', $berita->id) }}" class="text-dark text-decoration-none stretched-link">
                                                    {{ Str::limit($berita->judul, 70) }}
                                                </a>
                                            </h5>
                                            <p class="text-secondary small mb-0 d-none d-md-block">{{ Str::limit(strip_tags($berita->isi), 75) }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4 d-sm-none">
                    <a href="{{ route('berita') }}" class="btn btn-outline-primary fw-bold">Lihat Semua</a>
                </div>
            @else
                <div class="home-empty-state">
                    <div class="home-empty-state-icon">
                        <x-admin-icon name="news" size="48"/>
                    </div>
                    <h4>Belum Ada Berita Terbaru</h4>
                    <p>Informasi terbaru dari sekolah akan ditampilkan di sini.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Penghargaan terbaru, bersumber dari data Prestasi -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <div class="section-title mb-1" style="font-size: 1.4rem;">Penghargaan & Prestasi</div>
                    <p class="text-secondary small mb-0">Pencapaian terbaru siswa-siswi kami</p>
                </div>
                <a href="{{ route('prestasi') }}" class="btn btn-outline-primary rounded-1 btn-sm fw-bold">Lihat Semua</a>
            </div>
            <div class="row g-4">
                @forelse($prestasis as $prestasi)
                    <div class="col-md-6 col-lg-3">
                        <article class="card bg-white shadow-sm border-0 h-100 group-hover rounded-4 overflow-hidden">
                            <div class="position-relative rounded-top-4 overflow-hidden mb-3" style="height: 200px;">
                                @if($prestasi->gambar)
                                    <img src="{{ asset('storage/' . $prestasi->gambar) }}" class="w-100 h-100"
                                        style="object-fit: cover;" alt="{{ $prestasi->judul }}">
                                @else
                                    <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="padding: 20px;">
                                        <x-admin-icon name="award" size="80" style="opacity: 0.35;"/>
                                    </div>
                                @endif
                                <div
                                    class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center opacity-0 hover-opacity-100 transition-all">
                                    <span
                                        class="btn btn-warning rounded-pill btn-sm fw-bold border-0">{{ \Carbon\Carbon::parse($prestasi->tanggal)->format('Y') }}</span>
                                </div>
                            </div>
                            <div class="p-3 pt-0">
                                <span class="badge rounded-pill mb-2 px-3 py-2" style="background: #e8eefc; color: #172554;">
                                    {{ \App\Models\Prestasi::KATEGORI[$prestasi->kategori] ?? ucfirst($prestasi->kategori) }}
                                </span>
                                <h5 class="fw-bold mb-1" style="font-size: 1.1rem;">
                                    <a href="{{ route('prestasi') }}#kategori-{{ $prestasi->kategori }}"
                                       class="text-dark text-decoration-none stretched-link">
                                        {{ $prestasi->judul }}
                                    </a>
                                </h5>
                                <p class="text-dark small fw-semibold mb-1">
                                    <x-admin-icon name="user" size="14" class="me-1 text-secondary"/>{{ $prestasi->nama_siswa ?: 'Nama siswa belum diisi' }}
                                </p>
                                <p class="small fw-bold mb-1" style="color: #172554;">
                                    <x-admin-icon name="award" size="14" class="me-1"/>{{ $prestasi->prestasi_medali ?: 'Prestasi belum diisi' }}
                                </p>
                                <p class="text-secondary small mb-0">
                                    {{ $prestasi->penyelenggara ?: Str::limit($prestasi->deskripsi, 50) }}
                                </p>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="home-empty-state">
                            <div class="home-empty-state-icon">
                                <x-admin-icon name="award" size="48"/>
                            </div>
                            <h4>Belum Ada Data Prestasi</h4>
                            <p>Pencapaian terbaru siswa akan ditampilkan di sini.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        .home-empty-state {
            min-height: 220px;
            padding: 2.5rem 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }

        .home-empty-state-icon {
            margin-bottom: 1rem;
            color: #64748b;
            opacity: 0.72;
        }

        .home-empty-state h4 {
            margin-bottom: 0.5rem;
            color: #0f172a;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .home-empty-state p {
            margin-bottom: 0;
            color: #64748b;
            font-size: 0.9rem;
        }

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

        .hover-primary-link:hover h6 {
            color: #172554 !important;
        }

        .hover-primary-link:hover p {
            color: #2563eb !important;
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
    <section id="kontak" class="py-5" style="background-color: #f8fafc;">
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
                        <a href="https://www.google.com/maps/search/?api=1&query=SD+Muhammadiyah+Komplek+Kolombo" target="_blank" class="d-flex align-items-start gap-3 text-decoration-none hover-primary-link">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Alamat Sekolah</h6>
                                <p class="text-secondary mb-0 small">
                                    {{ $settings['alamat'] ?? 'Jl. Rajawali No. 10, Demangan Baru, Depok, Sleman, Yogyakarta' }}</p>
                            </div>
                        </a>
                        <div class="d-flex align-items-start gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Layanan Telepon</h6>
                                <p class="text-secondary mb-0 small">{{ $settings['telepon'] ?? '(0274) 585755' }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Dukungan Email</h6>
                                <p class="text-secondary mb-0 small">{{ $settings['email'] ?? 'sdmuhkkolombo@gmail.com' }}
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
                const welcomeImage = document.querySelector('.welcome-image-container[data-tilt]');
                const canTilt = window.matchMedia('(hover: hover) and (pointer: fine)').matches
                    && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                if (welcomeImage && canTilt) {
                    let tiltFrame;

                    welcomeImage.addEventListener('pointermove', function (event) {
                        const bounds = welcomeImage.getBoundingClientRect();
                        const x = (event.clientX - bounds.left) / bounds.width - 0.5;
                        const y = (event.clientY - bounds.top) / bounds.height - 0.5;

                        cancelAnimationFrame(tiltFrame);
                        tiltFrame = requestAnimationFrame(function () {
                            welcomeImage.style.transform = `perspective(1000px) rotateX(${-y * 7}deg) rotateY(${x * 7}deg) scale3d(1.01, 1.01, 1.01)`;
                        });
                    });

                    welcomeImage.addEventListener('pointerleave', function () {
                        cancelAnimationFrame(tiltFrame);
                        welcomeImage.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                    });
                }

                const aboutImage = document.querySelector('.profil-grid-item[data-parallax]');

                if (aboutImage && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    let parallaxFrame;

                    const updateAboutParallax = function () {
                        const bounds = aboutImage.getBoundingClientRect();
                        const viewportCenter = window.innerHeight / 2;
                        const elementCenter = bounds.top + bounds.height / 2;
                        const progress = Math.max(-1, Math.min(1, (viewportCenter - elementCenter) / window.innerHeight));
                        aboutImage.style.setProperty('--profil-parallax-y', `${progress * 14}px`);
                    };

                    const requestAboutParallax = function () {
                        cancelAnimationFrame(parallaxFrame);
                        parallaxFrame = requestAnimationFrame(updateAboutParallax);
                    };

                    window.addEventListener('scroll', requestAboutParallax, { passive: true });
                    window.addEventListener('resize', requestAboutParallax);
                    requestAboutParallax();
                }

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
