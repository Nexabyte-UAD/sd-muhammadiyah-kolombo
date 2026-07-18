{{--
    Halaman Sertifikat Akreditasi Publik (pages/akreditasi.blade.php)
    Menampilkan salinan/gambar sertifikat akreditasi resmi sekolah yang diunggah oleh admin,
    lengkap dengan status fall-back jika gambar sertifikat belum tersedia.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Akreditasi</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                
                <!-- Judul -->
                <h2 class="fw-bold text-dark mb-4">
                    {{ optional($profil)->judul ?? 'Sertifikat Akreditasi' }}
                </h2>

                <!-- Grid Foto Sertifikat -->
                <div class="rounded-4 border p-3 mx-auto" style="max-width: 750px; min-height: 420px; background: #f1f3f5; border-color: #dbe2e8 !important;">
                    @if(isset($profil) && $profil->gambar && file_exists(public_path('storage/' . $profil->gambar)))
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="d-block w-100 h-100 rounded-3 bg-white" style="max-height: 620px; object-fit: contain;" alt="Sertifikat Akreditasi SD Muhammadiyah Kolombo">
                    @else
                        <img src="{{ asset('assets/images/no-image-available.jpg') }}" class="d-block w-100 rounded-3" style="height: 388px; object-fit: contain; object-position: center; mix-blend-mode: multiply;" alt="Gambar tidak tersedia">
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection
