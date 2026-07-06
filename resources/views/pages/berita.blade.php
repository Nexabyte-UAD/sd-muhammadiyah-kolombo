@extends('layouts.public')

@section('content')
<x-breadcrumb>Papan Berita</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Papan Berita</h2>
            <p class="text-secondary mb-0">
                Kumpulan informasi, pengumuman, dan artikel terbaru dari SD Muhammadiyah Komplek Kolombo.
            </p>
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
                                <i class="bi bi-newspaper text-secondary opacity-50" style="font-size: 3.5rem;"></i>
                            </div>
                        @endif

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                <span class="badge rounded-pill px-3 py-2"
                                      style="background: #e8eefc; color: #172554;">
                                    Informasi
                                </span>
                                <span class="small text-secondary">
                                    <i class="bi bi-calendar3 me-1"></i>
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
                        <i class="bi bi-newspaper fs-1 text-secondary opacity-25 d-block mb-3"></i>
                        <h5 class="fw-bold text-dark mb-2">Belum Ada Berita</h5>
                        <p class="text-secondary mb-0">Saat ini belum ada publikasi berita terbaru.</p>
                    </div>
                </div>
            @endforelse

            @if($beritas->hasPages())
                <div class="col-12 d-flex justify-content-center mt-5">
                    {{ $beritas->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
