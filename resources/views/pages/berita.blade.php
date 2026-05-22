@extends('layouts.public')

@section('content')
<x-breadcrumb>Papan Berita</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-dark mb-3">Papan Berita</h2>
                <p class="text-secondary" style="font-size: 1.05rem;">
                    Kumpulan informasi, pengumuman, dan artikel terbaru dari SD Muhammadiyah Kolombo.
                </p>
            </div>
        </div>
        <div class="row g-4">
            @forelse($beritas as $berita)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    @if($berita->gambar)
                    <img src="{{ asset('storage/' . $berita->gambar) }}" class="card-img-top border-bottom"
                        alt="{{ $berita->judul }}" style="height: 220px; object-fit: cover;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center border-bottom"
                        style="height: 220px;">
                        <i class="bi bi-image text-secondary opacity-25" style="font-size: 4rem;"></i>
                    </div>
                    @endif
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-3">
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-medium">Informasi</span>
                            <small class="text-secondary ms-2 fw-medium"><i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($berita->tanggal)->translatedFormat('d F Y') }}</small>
                        </div>
                        <h5 class="card-title fw-bold text-dark mb-3" style="line-height: 1.5;">
                            <a href="{{ route('berita.detail', $berita->id) }}"
                                class="text-dark text-decoration-none stretched-link">{{ $berita->judul }}</a>
                        </h5>
                        <p class="card-text text-secondary mb-0 flex-grow-1"
                            style="font-size: 0.95rem; line-height: 1.6;">
                            {{ Str::limit(strip_tags($berita->isi), 100) }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-light rounded-4">
                    <i class="bi bi-inbox fs-1 text-secondary mb-3"></i>
                    <h5>Belum Ada Berita</h5>
                    <p class="text-secondary">Saat ini belum ada publikasi berita terbaru.</p>
                </div>
            </div>
            @endforelse

            <div class="col-12 d-flex justify-content-center mt-5">
                {{ $beritas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</section>
@endsection