@extends('layouts.public')

@section('content')
<x-breadcrumb>Struktural: {{ ucfirst($tipe) }}</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-dark mb-3">Struktural: {{ ucfirst($tipe) }}</h2>
                <p class="text-secondary" style="font-size: 1.05rem;">
                    Daftar tenaga pendidik dan kependidikan di lingkungan SD Muhammadiyah Komplek Kolombo.
                </p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse($gurus as $guru)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white text-center">
                    @if($guru->foto)
                        <img src="{{ asset('storage/' . $guru->foto) }}" class="card-img-top w-100 border-bottom" style="height: 250px; object-fit: cover;" alt="{{ $guru->nama }}">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-secondary w-100 border-bottom" style="height: 250px;">
                            <i class="bi bi-person opacity-25" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-1" style="line-height: 1.4;">{{ $guru->nama }}</h5>
                        <div class="text-success fw-bold mb-3 small">{{ $guru->jabatan }}</div>
                        <p class="text-secondary small mb-1">NIP: {{ $guru->nip ?? '-' }}</p>
                        <p class="text-dark small mb-0 fw-medium">Mapel: {{ $guru->mapel ?? 'Semua' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <h5 class="text-secondary">Belum ada data tenaga pengajar.</h5>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
