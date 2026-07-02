@extends('layouts.public')

@section('content')
<x-breadcrumb>Prestasi Siswa</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        
        <!-- Header -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-dark mb-3">Prestasi Siswa</h2>
                <p class="text-secondary" style="font-size: 1.05rem;">
                    Catatan pencapaian dan penghargaan yang berhasil diraih oleh siswa-siswi SD Muhammadiyah Komplek Kolombo.
                </p>
            </div>
        </div>

        <!-- Grid Prestasi -->
        <div class="row g-4">
            @forelse($prestasis as $prestasi)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <!-- Foto -->
                    @if($prestasi->foto)
                        <img src="{{ asset('storage/' . $prestasi->foto) }}" class="card-img-top w-100 border-bottom" style="height: 240px; object-fit: cover;" alt="{{ $prestasi->judul }}">
                    @else
                        <!-- Placeholder jika tidak ada foto -->
                        <div class="bg-light d-flex align-items-center justify-content-center border-bottom" style="height: 240px;">
                            <i class="bi bi-trophy text-secondary opacity-25" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                    
                    <!-- Konten -->
                    <div class="card-body p-4">
                        <!-- Tahun -->
                        <div class="text-success fw-bold mb-2" style="font-size: 0.9rem;">
                            <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($prestasi->tanggal)->format('Y') }}
                        </div>
                        
                        <!-- Judul -->
                        <h5 class="card-title fw-bold text-dark mb-3" style="line-height: 1.4;">
                            {{ $prestasi->judul }}
                        </h5>
                        
                        <!-- Deskripsi -->
                        <div class="card-text text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ $prestasi->deskripsi }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 py-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 mb-3 opacity-50 d-block"></i>
                <p>Belum ada catatan prestasi yang dimasukkan saat ini.</p>
            </div>
            @endforelse
        </div>
        
    </div>
</section>
@endsection
