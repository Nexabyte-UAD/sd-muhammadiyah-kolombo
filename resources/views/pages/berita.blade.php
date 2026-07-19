{{--
    Halaman Galeri Berita & Artikel Publik (pages/berita.blade.php)
    Menampilkan daftar seluruh berita/pengumuman sekolah yang sudah diterbitkan.
    Dilengkapi form pencarian keyword berita, paginasi interaktif Bootstrap 5 berbentuk bulat minimalis,
    serta kartu rangkuman isi berita dan cover gambar.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Papan Berita</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Papan Berita</h2>
                <p class="text-secondary mb-0">
                    Kumpulan informasi, pengumuman, dan artikel terbaru dari SD Muhammadiyah Komplek Kolombo.
                </p>
            </div>
            <form action="{{ route('berita') }}" method="GET" class="d-flex align-items-center gap-2" style="max-width: 420px; width: 100%;">
                <input type="text" name="search" class="form-control" placeholder="Cari berita..." value="{{ $search ?? '' }}">
                <button class="btn btn-primary px-4" type="submit">Cari</button>
            </form>
        </div>

        <div class="row g-4">
            @forelse($beritas as $berita)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        @if($berita->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($berita->gambar))
                            <img src="{{ asset('storage/' . $berita->gambar) }}"
                                 class="card-img-top w-100 border-bottom"
                                 alt="{{ $berita->judul }}"
                                 style="height: 230px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center border-bottom bg-secondary bg-opacity-10"
                                 style="height: 230px;">
                                <x-admin-icon name="news" size="56" class="text-secondary opacity-50"/>
                            </div>
                        @endif

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                <span class="badge rounded-pill px-3 py-2"
                                      style="background: #e8eefc; color: #172554;">
                                    Informasi
                                </span>
                                <span class="small text-secondary">
                                    <x-admin-icon name="classes" size="14" class="me-1"/>
                                    {{ \Carbon\Carbon::parse($berita->tanggal)->translatedFormat('d F Y') }}
                                </span>
                            </div>

                            <h5 class="card-title fw-bold text-dark mb-3" style="line-height: 1.4;">
                                <a href="{{ route('berita.detail', $berita->id) }}"
                                   class="text-dark text-decoration-none stretched-link">
                                    {{ $berita->judul }}
                                </a>
                            </h5>

                            <p class="card-text text-secondary mb-0 flex-grow-1"
                               style="font-size: 0.95rem; line-height: 1.7;">
                                {{ Str::limit(strip_tags($berita->isi), 100) }}
                            </p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center rounded-4 border py-5 px-3 bg-light">
                        <x-admin-icon name="news" size="48" class="text-secondary opacity-25 mb-3"/>
                        @if(isset($search) && $search !== '')
                            <h5 class="fw-bold text-dark mb-2">Pencarian Tidak Ditemukan</h5>
                            <p class="text-secondary mb-0">Tidak ada berita yang cocok dengan kata kunci "{{ $search }}".</p>
                        @else
                            <h5 class="fw-bold text-dark mb-2">Belum Ada Berita</h5>
                            <p class="text-secondary mb-0">Saat ini belum ada publikasi berita terbaru.</p>
                        @endif
                    </div>
                </div>
            @endforelse

            @if($beritas->hasPages())
                <div class="col-12 d-flex justify-content-center mt-5 pagination-wrapper">
                    {{ $beritas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</section>

@push('styles')
<style>
    /* Minimalist Circular Pagination Styles */
    .pagination-wrapper .pagination {
        display: flex;
        gap: 8px;
        margin-bottom: 0;
        padding-left: 0;
        list-style: none;
        align-items: center;
    }
    .pagination-wrapper .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50% !important;
        border: none !important;
        color: #475569 !important; /* Muted Slate text */
        background-color: transparent !important;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .pagination-wrapper .page-item .page-link:hover {
        background-color: #f1f5f9 !important; /* Soft gray circular highlight */
        color: #0f172a !important;
    }
    .pagination-wrapper .page-item.active .page-link {
        background-color: #172554 !important; /* Active Navy background */
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(23, 37, 84, 0.2);
    }
    .pagination-wrapper .page-item.disabled .page-link {
        color: #cbd5e1 !important;
        background-color: transparent !important;
        pointer-events: none;
    }
    /* Hide default Laravel desktop "Showing X to Y results" block to prevent clutter */
    .pagination-wrapper p.small.text-muted {
        display: none !important;
    }
    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }
</style>
@endpush
@endsection
