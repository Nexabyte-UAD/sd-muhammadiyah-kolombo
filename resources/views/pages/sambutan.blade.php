@extends('layouts.public')

@section('content')
<x-breadcrumb>Kata Sambutan</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-4 text-center">
                <div class="rounded-4 overflow-hidden shadow-sm border mb-3">
                    @if(isset($profil) && $profil->gambar)
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="img-fluid w-100" style="object-fit: cover; height: 400px;" alt="Kepala Sekolah">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-secondary w-100" style="height: 400px;">
                            <i class="bi bi-person-badge" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                </div>
                <!-- Nama Kepala Sekolah bisa diambil dari profil->judul atau hardcoded -->
                <h5 class="fw-bold text-dark mt-3 mb-1">{{ optional($profil)->judul ?? 'Kepala Sekolah' }}</h5>
                <p class="text-secondary small">SD Muhammadiyah Komplek Kolombo</p>
            </div>
            <div class="col-lg-8">
                <h3 class="fw-bold text-dark mb-4" style="font-size: 1.8rem;">Kata Sambutan</h3>
                <div class="text-secondary" style="line-height: 1.8; font-size: 1rem;">
                    @if(isset($profil) && $profil->konten)
                        @if(strip_tags($profil->konten) !== $profil->konten)
                            {!! $profil->konten !!}
                        @else
                            {!! nl2br(e($profil->konten)) !!}
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
</section>
@endsection
