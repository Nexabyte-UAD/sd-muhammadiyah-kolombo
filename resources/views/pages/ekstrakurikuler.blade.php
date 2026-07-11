{{--
    Halaman Daftar Ekstrakurikuler Publik (pages/ekstrakurikuler.blade.php)
    Menampilkan seluruh daftar kegiatan ekstrakurikuler sekolah beserta nama pembina,
    jadwal latihan, dokumentasi foto, dan deskripsi tujuan kegiatan ekstrakurikuler.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Ekstrakurikuler</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Ekstrakurikuler</h2>
            <p class="text-secondary mb-0">
                Wadah pengembangan minat, bakat, keterampilan, dan karakter siswa di luar kegiatan pembelajaran.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($ekstrakurikulers as $ekskul)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="position-relative">
                            @if($ekskul->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($ekskul->foto))
                                <img src="{{ asset('storage/' . $ekskul->foto) }}"
                                     class="card-img-top w-100 border-bottom"
                                     style="height: 230px; object-fit: cover;"
                                     alt="{{ $ekskul->nama }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center border-bottom bg-secondary bg-opacity-10"
                                     style="height: 230px;">
                                    <i class="bi bi-activity text-secondary opacity-50" style="font-size: 3.5rem;"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark mb-3" style="line-height: 1.4;">
                                {{ $ekskul->nama }}
                            </h5>

                            <div class="small mb-3">
                                <div class="d-flex align-items-start gap-2 mb-2 text-secondary">
                                    <i class="bi bi-calendar3 mt-1" style="color: #172554;"></i>
                                    <span><span class="fw-semibold text-dark">Jadwal:</span> {{ $ekskul->jadwal }}</span>
                                </div>
                                @if($ekskul->pembina)
                                    <div class="d-flex align-items-start gap-2 text-secondary">
                                        <i class="bi bi-person-badge mt-1" style="color: #172554;"></i>
                                        <span><span class="fw-semibold text-dark">Pembina:</span> {{ $ekskul->pembina }}</span>
                                    </div>
                                @endif
                            </div>

                            <p class="text-secondary mb-0 flex-grow-1" style="font-size: 0.95rem; line-height: 1.7;">
                                {{ $ekskul->deskripsi }}
                            </p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center rounded-4 border py-5 px-3 bg-light">
                        <i class="bi bi-activity fs-1 text-secondary opacity-25 d-block mb-3"></i>
                        <h5 class="fw-bold text-dark mb-2">Belum Ada Ekstrakurikuler</h5>
                        <p class="text-secondary mb-0">Program ekstrakurikuler akan ditampilkan di halaman ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
