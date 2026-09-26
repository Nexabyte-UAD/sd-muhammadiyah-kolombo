@extends('layouts.public')

@section('content')
<x-breadcrumb>Galeri Foto</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Header & Filter -->
        <div class="mb-4 pb-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Galeri Foto</h2>
                <p class="text-secondary mb-0">
                    Dokumentasi momen dan kegiatan teranyar SD Muhammadiyah Komplek Kolombo.
                </p>
            </div>
            
            <form action="{{ route('galeri.foto') }}" method="GET" class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                <select name="kategori" class="form-select form-select-sm border-secondary-subtle" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" class="form-control form-control-sm border-secondary-subtle" placeholder="Cari foto..." value="{{ $search }}" style="width: 180px;">
            </form>
        </div>

        <!-- Grid Foto -->
        <div class="row g-4">
            @forelse($fotos as $foto)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <a href="{{ route('galeri.foto.detail', $foto) }}" class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden gallery-photo-card position-relative text-decoration-none d-block">
                        <div class="position-relative overflow-hidden" style="height: 220px;">
                            @if($foto->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($foto->foto))
                                <img src="{{ asset('storage/' . $foto->foto) }}" class="w-100 h-100 gallery-img" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $foto->judul }}">
                            @else
                                <div class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                    <x-admin-icon name="image" size="48" class="text-secondary opacity-50"/>
                                </div>
                            @endif
                            <div class="gallery-overlay d-flex flex-column justify-content-end p-3 text-white">
                                <span class="badge bg-primary text-uppercase align-self-start mb-2" style="font-size: 0.65rem;">{{ $foto->kategori }}</span>
                                <h6 class="fw-bold mb-1 lh-sm text-white" style="font-size: 0.95rem;">{{ Str::limit($foto->judul, 45) }}</h6>
                                @if($foto->tanggal)
                                    <small class="text-white-50" style="font-size: 0.75rem;"><x-admin-icon name="clock" size="12" class="me-1"/>{{ $foto->tanggal->translatedFormat('d M Y') }}</small>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center rounded-4 border py-5 px-3 bg-light">
                        <x-admin-icon name="camera" size="56" class="text-secondary opacity-25 mb-3"/>
                        <h5 class="fw-bold text-dark mb-2">Belum Ada Foto</h5>
                        <p class="text-secondary mb-0">Dokumentasi foto kegiatan sekolah akan ditampilkan di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-public-pagination :paginator="$fotos" />
    </div>
</section>
@endsection

@push('styles')
<style>
    .gallery-photo-card {
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gallery-photo-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12) !important;
    }
    .gallery-photo-card:hover .gallery-img {
        transform: scale(1.06);
    }
    .gallery-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0) 30%, rgba(15, 23, 42, 0.85) 100%);
        opacity: 0.9;
        transition: opacity 0.3s ease;
    }
    .gallery-photo-card:hover .gallery-overlay {
        opacity: 1;
    }
</style>
@endpush
