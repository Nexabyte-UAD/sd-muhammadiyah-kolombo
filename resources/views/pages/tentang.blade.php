@extends('layouts.public')

@section('content')
<x-breadcrumb>Tentang</x-breadcrumb>

<section class="py-5 bg-white min-vh-100">
    <div class="container">
        <div class="row align-items-start g-5">
            <!-- Sisi Kiri: Gambar Sticky -->
            <div class="col-lg-5 position-sticky" style="top: 130px;">
                <div class="rounded-4 overflow-hidden shadow-sm border p-2 bg-light">
                    @if(isset($profil) && $profil->gambar && file_exists(public_path('storage/' . $profil->gambar)))
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="img-fluid w-100 rounded-3" style="object-fit: cover; max-height: 500px;" alt="Foto Tentang Sekolah">
                    @else
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1000&auto=format&fit=crop" class="img-fluid w-100 rounded-3" style="object-fit: cover; max-height: 500px;" alt="School Building">
                    @endif
                </div>
            </div>
            
            <!-- Sisi Kanan: Teks Sejarah/Profil -->
            <div class="col-lg-7">
                <h6 class="text-uppercase fw-bold text-primary mb-2" style="font-size: 0.9rem; letter-spacing: 1.5px;">Tentang Sekolah</h6>
                <h3 class="fw-bold text-dark mb-4 lh-sm" style="font-size: 1.8rem; letter-spacing: -0.5px;">
                    Membentuk Generasi <span class="text-primary">{{ optional($profil)->judul ?? 'Islami & Berprestasi' }}</span>
                </h3>
                
                <div class="text-secondary" style="line-height: 1.8; font-size: 1rem; text-align: justify;">
                    @if(isset($profil) && $profil->konten)
                        @if(strip_tags($profil->konten) !== $profil->konten)
                            {!! $profil->konten !!}
                        @else
                            @foreach(explode("\n", str_replace("\r", "", $profil->konten)) as $paragraph)
                                @if(trim($paragraph))
                                    <p class="mb-3" style="text-indent: 2rem;">
                                        {{ trim($paragraph) }}
                                    </p>
                                @endif
                            @endforeach
                        @endif
                    @else
                        <p class="mb-3" style="text-indent: 2rem;">
                            SD Muhammadiyah Komplek Kolombo hadir sebagai lembaga pendidikan dasar Islam yang mengintegrasikan kurikulum ilmu pengetahuan umum dengan penanaman nilai-nilai adab dan akhlak Islam secara menyeluruh (holistik). Kami senantiasa berkomitmen untuk memberikan pembelajaran terbaik guna mendukung tumbuh kembang rohani, jasmani, dan intelektual putra-putri bangsa agar siap menghadapi tantangan zaman.
                        </p>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
