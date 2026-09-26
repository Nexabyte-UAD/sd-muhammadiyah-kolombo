{{--
    Halaman Daftar Ekstrakurikuler Publik (pages/ekstrakurikuler.blade.php)
    Menampilkan seluruh daftar kegiatan ekstrakurikuler sekolah dengan kartu seragam,
    preview ringkas, serta modal detail interaktif untuk melihat deskripsi lengkap.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Ekstrakurikuler</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Ekstrakurikuler</h2>
                <p class="text-secondary mb-0">
                    Wadah pengembangan minat, bakat, keterampilan, dan karakter siswa di luar kegiatan pembelajaran.
                </p>
            </div>
            <form action="{{ route('ekstrakurikuler') }}" method="GET" class="d-flex align-items-center justify-content-md-end gap-2 extracurricular-search-form">
                <label for="extracurricular-search-input" class="text-secondary small">Search:</label>
                <input type="search" id="extracurricular-search-input" name="search"
                       class="form-control form-control-sm border-secondary-subtle"
                       value="{{ $search }}" enterkeyhint="search"
                       aria-label="Cari ekstrakurikuler">
            </form>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($ekstrakurikulers as $ekskul)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white d-flex flex-column">
                        <div class="position-relative">
                            @if($ekskul->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($ekskul->foto))
                                <img src="{{ asset('storage/' . $ekskul->foto) }}"
                                     class="card-img-top w-100 border-bottom"
                                     style="height: 220px; object-fit: cover;"
                                     alt="{{ $ekskul->nama }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center border-bottom bg-secondary bg-opacity-10"
                                     style="height: 220px;">
                                    <x-admin-icon name="ekstrakurikuler" size="56" class="text-secondary opacity-50"/>
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <h5 class="card-title fw-bold text-dark mb-3" style="line-height: 1.4;">
                                {{ $ekskul->nama }}
                            </h5>

                            <div class="small mb-3">
                                <div class="d-flex align-items-start gap-2 mb-2 text-secondary">
                                    <x-admin-icon name="classes" size="15" class="mt-1" style="color: #172554;"/>
                                    <span><span class="fw-semibold text-dark">Jadwal:</span> {{ $ekskul->jadwal }}</span>
                                </div>
                                @if($ekskul->pembina)
                                    <div class="d-flex align-items-start gap-2 text-secondary">
                                        <x-admin-icon name="person-badge" size="15" class="mt-1" style="color: #172554;"/>
                                        <span><span class="fw-semibold text-dark">Pembina:</span> {{ $ekskul->pembina }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="text-secondary mb-3 flex-grow-1 ekskul-card-preview" style="font-size: 0.95rem; line-height: 1.6;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($ekskul->deskripsi), 125, '...') }}
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm rounded-3 mt-auto w-100 fw-semibold py-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEkskul{{ $ekskul->id }}">
                                Lihat Selengkapnya &rarr;
                            </button>
                        </div>
                    </article>
                </div>

                <!-- Modal Detail Ekstrakurikuler -->
                <div class="modal fade" id="modalEkskul{{ $ekskul->id }}" tabindex="-1" aria-labelledby="modalEkskulLabel{{ $ekskul->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header border-0 pb-0" style="background-color: #f8fafc;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill bg-primary px-3 py-2" style="font-size: 0.8rem;">Ekstrakurikuler</span>
                                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalEkskulLabel{{ $ekskul->id }}">{{ $ekskul->nama }}</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if($ekskul->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($ekskul->foto))
                                    <div class="rounded-3 overflow-hidden mb-4 border text-center bg-light">
                                        <img src="{{ asset('storage/' . $ekskul->foto) }}" class="img-fluid w-100" style="max-height: 380px; object-fit: cover;" alt="{{ $ekskul->nama }}">
                                    </div>
                                @endif

                                <div class="p-3 bg-light rounded-3 mb-4 d-flex flex-column flex-md-row gap-3 justify-content-around border">
                                    <div class="d-flex align-items-center gap-2 text-secondary">
                                        <x-admin-icon name="classes" size="18" style="color: #172554;"/>
                                        <span><strong class="text-dark">Jadwal Latihan:</strong> {{ $ekskul->jadwal }}</span>
                                    </div>
                                    @if($ekskul->pembina)
                                        <div class="d-flex align-items-center gap-2 text-secondary">
                                            <x-admin-icon name="person-badge" size="18" style="color: #172554;"/>
                                            <span><strong class="text-dark">Pembina / Pelatih:</strong> {{ $ekskul->pembina }}</span>
                                        </div>
                                    @endif
                                </div>

                                <h6 class="fw-bold text-dark mb-2">Deskripsi & Tujuan Kegiatan:</h6>
                                <div class="text-secondary ck-content" style="font-size: 0.98rem; line-height: 1.8;">
                                    @if(strip_tags($ekskul->deskripsi) !== $ekskul->deskripsi)
                                        {!! $ekskul->deskripsi !!}
                                    @else
                                        {!! nl2br(e($ekskul->deskripsi)) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 bg-light">
                                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center rounded-4 border py-5 px-3 bg-light">
                        <x-admin-icon name="ekstrakurikuler" size="48" class="text-secondary opacity-25 mb-3"/>
                        @if($search !== '')
                            <h5 class="fw-bold text-dark mb-2">Pencarian Tidak Ditemukan</h5>
                            <p class="text-secondary mb-0">Tidak ada ekstrakurikuler yang cocok dengan kata kunci "{{ $search }}".</p>
                        @else
                            <h5 class="fw-bold text-dark mb-2">Belum Ada Ekstrakurikuler</h5>
                            <p class="text-secondary mb-0">Program ekstrakurikuler akan ditampilkan di halaman ini.</p>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .extracurricular-search-form { width: 100%; }
    .extracurricular-search-form .form-control { width: 180px; }

    .ekskul-card-preview {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 767.98px) {
        .extracurricular-search-form .form-control {
            width: 100%;
            min-width: 0;
        }
    }
</style>
@endpush
