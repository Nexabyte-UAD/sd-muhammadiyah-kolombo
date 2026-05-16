@extends('layouts.public')

@section('content')
<x-breadcrumb>Ekstrakurikuler</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-dark mb-3">Ekstrakurikuler</h2>
                <p class="text-secondary" style="font-size: 1.05rem;">
                    Daftar kegiatan ekstrakurikuler untuk mengembangkan minat dan bakat siswa.
                </p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse($ekstrakurikulers as $ekskul)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    @if($ekskul->foto)
                        <img src="{{ asset('storage/' . $ekskul->foto) }}" style="height: 220px; object-fit: cover;" class="card-img-top border-bottom">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center border-bottom" style="height: 220px;">
                            <i class="bi bi-bicycle text-secondary opacity-25" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-3">{{ $ekskul->nama }}</h5>
                        <div class="d-flex align-items-center text-success mb-3 small fw-medium">
                            <i class="bi bi-clock me-2"></i> Jadwal: {{ $ekskul->jadwal }}
                        </div>
                        <p class="text-secondary mb-0" style="font-size: 0.95rem; line-height: 1.6;">{{ $ekskul->deskripsi }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">Belum ada jadwal ekstrakurikuler terdaftar.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
