{{--
    Halaman Kata Sambutan Kepala Sekolah Publik (pages/sambutan.blade.php)
    Menampilkan sambutan resmi tertulis dari kepala sekolah di samping foto beliau,
    dengan nama dan jabatan terpusat di bawah foto.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Kata Sambutan</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container py-4">
        @php
            $sData = isset($profil) ? $profil->sambutanParts() : (new \App\Models\ProfilSekolah())->sambutanParts();
        @endphp

        <div class="row align-items-start g-5">
            <!-- Kolom Kiri: Foto & Nama Kepala Sekolah di Bawah Foto -->
            <div class="col-lg-4 text-center">
                <div class="rounded-4 overflow-hidden border mb-3 shadow-sm mx-auto" style="max-width: 320px; background: #f8fafc;">
                    @if(isset($profil) && $profil->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($profil->gambar))
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="d-block w-100 img-fluid" style="max-height: 420px; object-fit: cover; object-position: top center;" alt="{{ $sData['nama'] }}">
                    @else
                        <div class="w-100 bg-light position-relative d-flex align-items-center justify-content-center" style="height: 380px;">
                            <svg width="96" height="96" viewBox="0 0 16 16" fill="currentColor" class="text-secondary opacity-25" aria-hidden="true">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <h5 class="fw-bold text-dark mt-3 mb-1" style="font-size: 1.15rem; color: #0f172a;">{{ $sData['nama'] }}</h5>
                <p class="text-secondary small mb-0" style="font-weight: 500;">{{ $sData['sub_judul'] }}</p>
            </div>

            <!-- Kolom Kanan: Isi Teks Kata Sambutan di Samping Foto -->
            <div class="col-lg-8">
                <div class="ps-lg-3">
                    <h2 class="fw-bold text-dark mb-4" style="font-size: 2rem; letter-spacing: -0.5px;">Kata Sambutan Kepala Sekolah</h2>
                    <div class="text-secondary ck-content" style="line-height: 1.85; font-size: 1.05rem; color: #334155;">
                        @if($sData['konten'])
                            @if(strip_tags($sData['konten']) !== $sData['konten'])
                                {!! $sData['konten'] !!}
                            @else
                                {!! nl2br(e($sData['konten'])) !!}
                            @endif
                        @else
                            <p>Assalamualaikum Warahmatullahi Wabarakatuh,</p>
                            <p>Selamat datang di website resmi SD Muhammadiyah Komplek Kolombo. Puji syukur kita panjatkan ke hadirat Allah SWT atas segala limpahan rahmat dan karunia-Nya.</p>
                            <p>Website ini hadir sebagai media informasi, komunikasi, dan pertanggungjawaban publik dari sekolah kami. Kami berkomitmen untuk terus menghadirkan pendidikan dasar Islam yang unggul, menyenangkan, dan relevan dengan perkembangan zaman.</p>
                            <p>Terima kasih atas kepercayaan masyarakat. Mari bersama-sama bersinergi mencetak generasi cerdas, berprestasi, dan berakhlakul karimah.</p>
                            <p>Wassalamualaikum Warahmatullahi Wabarakatuh.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
