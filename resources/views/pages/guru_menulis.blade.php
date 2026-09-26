@extends('layouts.public')

@section('content')
<x-breadcrumb>Guru Menulis</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Header & Filter -->
        <div class="mb-4 pb-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Guru Menulis</h2>
                <p class="text-secondary mb-0">
                    Kumpulan karya tulis, opini, dan artikel edukatif dari bapak & ibu guru SD Muhammadiyah Komplek Kolombo.
                </p>
            </div>
            
            <form action="{{ route('guru-menulis') }}" method="GET" class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                <select name="kategori" class="form-select form-select-sm border-secondary-subtle" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" class="form-control form-control-sm border-secondary-subtle" placeholder="Cari artikel..." value="{{ $search }}" style="width: 180px;">
            </form>
        </div>

        <!-- Grid Artikel -->
        <div class="row g-4">
            @forelse($artikels as $artikel)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 rounded-4 overflow-hidden border article-card shadow-sm">
                        <div class="position-relative overflow-hidden bg-light" style="height: 210px;">
                            @if($artikel->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($artikel->gambar))
                                <img src="{{ asset('storage/' . $artikel->gambar) }}" class="w-100 h-100 article-img" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $artikel->judul }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light">
                                    <x-admin-icon name="pencil" size="40" class="opacity-50"/>
                                </div>
                            @endif
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="text-uppercase fw-bold text-primary mb-1 d-block" style="font-size: 0.725rem; letter-spacing: 0.5px;">
                                {{ $artikel->kategori }}
                            </span>
                            <h5 class="card-title fw-bold mb-2 lh-base" style="font-size: 1.1rem; color: #0f172a;">
                                <a href="{{ route('guru-menulis.detail', $artikel->slug) }}" class="text-dark text-decoration-none article-title-link">
                                    {{ Str::limit($artikel->judul, 65) }}
                                </a>
                            </h5>
                            <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.6; color: #475569 !important;">
                                {{ Str::limit(strip_tags($artikel->isi), 95) }}
                            </p>
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between text-secondary small">
                                <span class="fw-medium text-dark">{{ $artikel->penulis }}</span>
                                <span>{{ optional($artikel->tanggal)->translatedFormat('d M Y') ?? $artikel->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center rounded-4 border py-5 px-3 bg-light">
                        <x-admin-icon name="pencil" size="48" class="text-secondary opacity-25 mb-3"/>
                        <h5 class="fw-bold text-dark mb-2">Belum Ada Artikel</h5>
                        <p class="text-secondary mb-0">Karya tulis dan artikel guru akan segera dipublikasikan di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-public-pagination :paginator="$artikels" />
    </div>
</section>
@endsection

@push('styles')
<style>
    .article-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border-color: #e2e8f0 !important;
    }
    .article-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08) !important;
    }
    .article-card:hover .article-img {
        transform: scale(1.04);
    }
    .article-title-link {
        transition: color 0.2s ease;
    }
    .article-title-link:hover {
        color: #0284c7 !important;
    }
</style>
@endpush
