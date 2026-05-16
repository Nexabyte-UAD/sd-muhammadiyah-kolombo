@extends('layouts.public')

@section('content')
<x-breadcrumb>Visi & Misi</x-breadcrumb>

@php
    // Logika Pintar untuk Memisahkan Teks Visi dan Misi dari 1 Database Column
    $rawKonten = optional($profil)->konten ?? '';
    
    // Pisahkan teks berdasarkan kata "Misi" atau "Misi:"
    $parts = preg_split('/(?i)(Misi\s*:?\s*)/', $rawKonten);
    
    // Ambil bagian pertama sebagai Visi
    $visiText = trim($parts[0] ?? '');
    // Hapus kata "Visi:" di awal kalimat jika ada
    $visiText = preg_replace('/(?i)^(Visi\s*:?\s*)/', '', $visiText);
    if(empty($visiText)) $visiText = 'Belum ada data visi.';
    
    // Ambil bagian kedua sebagai Misi
    $misiText = trim($parts[1] ?? '');
    if(empty($misiText)) {
        $misiText = 'Belum ada data misi.';
    }
@endphp

<section class="py-5 bg-white min-vh-100">
    <div class="container">
        
        <!-- Gambar Utama (Jika Ada) -->
        @if(isset($profil) && $profil->gambar)
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="rounded-4 overflow-hidden shadow-sm border">
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="w-100" style="max-height: 400px; object-fit: cover;" alt="Visi Misi">
                    </div>
                </div>
            </div>
        @endif

        <div class="row justify-content-center g-4">
            
            <!-- Grid Visi (Atas) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 h-100" style="width: 6px; background-color: #1e3a8a;"></div>
                    <div class="card-body p-4 p-md-5 ps-md-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="bi bi-eye text-primary fs-4"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">Visi Kami</h3>
                        </div>
                        <div class="text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                            {!! nl2br(e($visiText)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Misi (Bawah) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 h-100" style="width: 6px; background-color: #2563eb;"></div>
                    <div class="card-body p-4 p-md-5 ps-md-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="bi bi-bullseye text-success fs-4"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">Misi Kami</h3>
                        </div>
                        <div class="text-secondary" style="font-size: 1.05rem; line-height: 1.8;">
                            {!! nl2br(e($misiText)) !!}
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</section>
@endsection
