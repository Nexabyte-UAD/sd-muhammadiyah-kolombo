@extends('layouts.public')

@section('content')
<x-breadcrumb>Tentang</x-breadcrumb>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="rounded-4 overflow-hidden shadow-sm">
                    @if(isset($profil) && $profil->gambar)
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="img-fluid w-100" style="object-fit: cover; height: 400px;" alt="School Building">
                    @else
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1000&auto=format&fit=crop" class="img-fluid w-100" style="object-fit: cover; height: 400px;" alt="School Building">
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <h6 class="text-uppercase fw-bold text-primary mb-2" style="font-size: 0.9rem; letter-spacing: 1px;">Profil Singkat</h6>
                <h3 class="fw-bold text-dark mb-4 lh-sm" style="font-size: 1.6rem;">Membentuk Generasi <span class="text-primary">{{ $settings['beranda_profil_judul'] ?? 'Islami & Berprestasi' }}</span></h3>
                
                <div class="text-secondary" style="line-height: 1.6; font-size: 0.95rem;">
                    <p>
                        {{ $settings['beranda_profil_teks'] ?? ($settings['nama_sekolah'] ?? 'SD Muhammadiyah Kolombo' . ' hadir sebagai bangku pendidikan dasar yang mengintegrasikan kurikulum ilmu pengetahuan mutakhir dengan penanaman nilai-nilai adab dan akhlak Islam secara menyeluruh (holistik). Kami senantiasa berkomitmen untuk memberikan pembelajaran terbaik bagi tumbuh kembang putra-putri bangsa.') }}
                    </p>
                    <div class="mt-4">
                        @if(optional($profil)->judul)
                            <h5 class="fw-bold mb-3 text-dark">{{ $profil->judul }}</h5>
                        @endif
                        {!! nl2br(e(optional($profil)->konten ?? '')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
