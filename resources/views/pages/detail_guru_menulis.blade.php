@extends('layouts.public')

@section('content')
<x-breadcrumb>Guru Menulis</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Kolom Utama Baca Artikel (Kiri) -->
            <div class="col-lg-8">
                <article class="pe-lg-3">
                    <!-- Kategori & Judul Artikel -->
                    <div class="mb-4">
                        <div class="text-uppercase fw-bold text-primary mb-2" style="font-size: 0.8rem; letter-spacing: 1px;">
                            {{ $guruMenulis->kategori }}
                        </div>
                        <h1 class="fw-bold mb-3" style="font-size: 2.25rem; line-height: 1.3; color: #0f172a;">
                            {{ $guruMenulis->judul }}
                        </h1>
                        
                        <div class="d-flex align-items-center gap-2 text-secondary small pb-3 border-bottom">
                            <span class="text-dark fw-medium">Oleh {{ $guruMenulis->penulis }}</span>
                            <span>•</span>
                            <span>{{ optional($guruMenulis->tanggal)->translatedFormat('d F Y') ?? $guruMenulis->created_at->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    <!-- Gambar Utama Artikel -->
                    @if($guruMenulis->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($guruMenulis->gambar))
                        <div class="rounded-3 overflow-hidden mb-4 border shadow-sm" style="max-height: 460px;">
                            <img src="{{ asset('storage/' . $guruMenulis->gambar) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $guruMenulis->judul }}">
                        </div>
                    @endif

                    <!-- Isi Artikel -->
                    <div class="article-body mb-4" style="font-size: 1.1rem; line-height: 1.9; color: #334155;">
                        @if(strip_tags($guruMenulis->isi) !== $guruMenulis->isi)
                            {!! $guruMenulis->isi !!}
                        @else
                            {!! nl2br(e($guruMenulis->isi)) !!}
                        @endif
                    </div>

                    <!-- Bagikan Artikel Ini -->
                    <div class="py-4 px-3 mb-5 rounded-4 bg-white text-center border shadow-sm">
                        <div class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; color: #475569; font-size: 0.95rem;">
                            BAGIKAN ARTIKEL INI
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                               target="_blank" 
                               class="btn rounded-pill px-4 py-2 bg-white d-inline-flex align-items-center gap-2 text-decoration-none share-btn-fb">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12c0-5.523-4.477-10-10-10z"/></svg>
                                <span>Facebook</span>
                            </a>

                            <!-- WhatsApp -->
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($guruMenulis->judul . ' - ' . request()->url()) }}" 
                               target="_blank" 
                               class="btn rounded-pill px-4 py-2 bg-white d-inline-flex align-items-center gap-2 text-decoration-none share-btn-wa">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>WhatsApp</span>
                            </a>

                            <!-- Twitter / X -->
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($guruMenulis->judul) }}&url={{ urlencode(request()->url()) }}" 
                               target="_blank" 
                               class="btn rounded-pill px-4 py-2 bg-white d-inline-flex align-items-center gap-2 text-decoration-none share-btn-tw">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                <span>Twitter</span>
                            </a>
                        </div>
                    </div>

                    <!-- Footer Artikel / Tombol Kembali -->
                    <div class="pt-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ route('guru-menulis') }}" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-medium">
                            ← Kembali ke Karya Guru
                        </a>
                        <span class="text-muted small">SD Muhammadiyah Komplek Kolombo</span>
                    </div>
                </article>
            </div>

            <!-- Sidebar Artikel Lainnya (Kanan) -->
            <div class="col-lg-4">
                <div class="ps-lg-2">
                    <div class="p-4 rounded-4 bg-white border shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                        <!-- Judul dengan Garis Kuning di Bawah -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; color: #0f172a !important;">
                                Artikel Lainnya
                            </h5>
                            <div style="width: 50px; height: 4px; background-color: #FEF102; border-radius: 2px;"></div>
                        </div>

                        @if($recentArtikels->isNotEmpty())
                            <div class="d-flex flex-column gap-2">
                                @foreach($recentArtikels as $item)
                                    <a href="{{ route('guru-menulis.detail', $item->slug) }}" class="text-decoration-none side-article-row p-2 rounded-3 d-flex gap-3 align-items-start">
                                        <div class="rounded-3 overflow-hidden flex-shrink-0 bg-light border" style="width: 72px; height: 72px;">
                                            @if($item->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->gambar))
                                                <img src="{{ asset('storage/' . $item->gambar) }}" class="w-100 h-100" style="object-fit: cover;" alt="">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-secondary">
                                                    <x-admin-icon name="pencil" size="22"/>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-uppercase fw-semibold text-primary mb-1" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                                {{ $item->kategori }}
                                            </div>
                                            <h6 class="fw-bold mb-1 lh-sm side-article-title" style="font-size: 0.9rem; color: #0f172a;">
                                                {{ Str::limit($item->judul, 55) }}
                                            </h6>
                                            <div class="text-muted small" style="font-size: 0.775rem;">
                                                {{ $item->penulis }}
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-secondary small mb-0">Belum ada artikel lainnya.</p>
                        @endif

                        <div class="mt-4 pt-3 border-top text-center">
                            <a href="{{ route('guru-menulis') }}" class="btn btn-sm btn-dark w-100 rounded-3 font-medium">
                                Lihat Semua Guru Menulis
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .side-article-row {
        transition: background-color 0.25s ease, transform 0.25s ease;
    }
    .side-article-row:hover {
        background-color: #f8fafc !important;
        transform: translateX(4px);
    }
    .side-article-row:hover .side-article-title {
        color: #0284c7 !important;
    }

    .share-btn-fb {
        border: 1px solid #2563eb !important;
        color: #2563eb !important;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .share-btn-fb:hover {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    .share-btn-wa {
        border: 1px solid #16a34a !important;
        color: #16a34a !important;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .share-btn-wa:hover {
        background-color: #16a34a !important;
        color: #ffffff !important;
    }

    .share-btn-tw {
        border: 1px solid #0284c7 !important;
        color: #0284c7 !important;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .share-btn-tw:hover {
        background-color: #0284c7 !important;
        color: #ffffff !important;
    }
</style>
@endpush
