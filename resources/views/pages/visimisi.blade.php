{{-- Halaman Visi & Misi Sekolah Publik --}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Profil: Visi & Misi</x-breadcrumb>

@php
    $visiMisi = $profil?->visiMisiParts() ?? ['visi' => '', 'misi' => []];
    $visiText = $visiMisi['visi'] ?: 'Belum ada data visi.';
    $misiItems = $visiMisi['misi'] ?: ['Belum ada data misi.'];
@endphp

<section class="py-5 bg-white min-vh-100">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold text-dark mb-2">Visi & Misi</h2>
                <p class="text-secondary mb-0">
                    Arah pandang dan komitmen SD Muhammadiyah Komplek Kolombo dalam mewujudkan pendidikan dasar berkualitas.
                </p>
            </div>
        </div>

        @if(isset($profil) && $profil->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($profil->gambar))
            <div class="row mb-5">
                <div class="col-12">
                    <div class="rounded-3 overflow-hidden border border-light">
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="w-100" style="max-height: 400px; object-fit: cover;" alt="Visi Misi SD Muhammadiyah Komplek Kolombo">
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4 align-items-stretch">
            <div class="col-12">
                <div class="h-100 bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                    <h3 class="text-primary fw-bold text-uppercase tracking-wider mb-4" style="font-size: 0.9rem; letter-spacing: 1.5px;">Visi Sekolah</h3>
                    <p class="text-dark fs-3 fw-bold lh-sm mb-0 text-center" style="letter-spacing: -0.5px; font-family: 'Outfit', sans-serif; white-space: pre-line;">&ldquo;{{ $visiText }}&rdquo;</p>
                </div>
            </div>

            <div class="col-12">
                <div class="h-100 bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                    <h3 class="text-primary fw-bold text-uppercase tracking-wider mb-4" style="font-size: 0.9rem; letter-spacing: 1.5px;">Misi Sekolah</h3>
                    <div class="d-flex flex-column gap-3">
                        @foreach($misiItems as $index => $item)
                            <div class="d-flex align-items-start gap-3">
                                <div class="d-flex align-items-center justify-content-center text-primary rounded-circle fw-bold" style="width: 28px; height: 28px; font-size: 0.9rem; flex-shrink: 0; background-color: #eff6ff;">
                                    {{ $index + 1 }}
                                </div>
                                <div class="text-secondary lh-lg mb-0" style="font-size: 1.05rem; text-align: justify;">
                                    {{ $item }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
