{{--
    Halaman Detail Berita & Artikel Publik (pages/detail_berita.blade.php)
    Menampilkan detail penuh konten berita tertentu, termasuk judul lengkap, cover gambar berita,
    tanggal publikasi terformat lokal, serta tombol kembali ke daftar papan berita utama.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Detail Berita</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold text-dark mb-4" style="font-size: 2rem; letter-spacing: -0.5px; line-height: 1.4;">{{ $berita->judul }}</h2>
                
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-medium me-3">Informasi</span>
                    <span class="text-secondary small fw-medium me-3"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($berita->tanggal)->translatedFormat('l, d F Y') }}</span>
                    <span class="text-secondary small fw-medium"><i class="bi bi-person me-1"></i> Admin</span>
                </div>
                
                @if($berita->gambar)
                    <div class="mb-5 rounded-4 overflow-hidden shadow-sm border p-2 bg-light">
                        <img src="{{ asset('storage/' . $berita->gambar) }}" class="img-fluid w-100 rounded-3" alt="{{ $berita->judul }}">
                    </div>
                @endif
                
                <div class="text-secondary" style="font-size: 1.05rem; line-height: 1.8;">
                    @if(strip_tags($berita->isi) !== $berita->isi)
                        {!! $berita->isi !!}
                    @else
                        {!! nl2br(e($berita->isi)) !!}
                    @endif
                </div>
                
                <div class="mt-5 pt-4 border-top">
                    <a href="{{ route('berita') }}" class="btn btn-outline-primary px-4">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
