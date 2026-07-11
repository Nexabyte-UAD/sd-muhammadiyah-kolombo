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

                <!-- Foto Sertifikat -->
                @if(isset($profil) && $profil->gambar)
                    <div class="shadow-sm rounded-4 overflow-hidden border p-2 bg-light mx-auto" style="max-width: 750px;">
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="img-fluid w-100 rounded-3" alt="Sertifikat Akreditasi SD Muhammadiyah Kolombo">
                    </div>
                @else
                    <div class="py-5 bg-light rounded-4 border text-secondary mx-auto" style="max-width: 750px;">
                        <i class="bi bi-file-earmark-image display-1 mb-3 opacity-50"></i>
                        <h5>Belum Ada Foto Sertifikat</h5>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</section>
@endsection
