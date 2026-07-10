@extends('layouts.public')

@section('content')
<x-breadcrumb>Prestasi Siswa</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Prestasi Siswa</h2>
            <p class="text-secondary mb-0">
                Pencapaian siswa dalam bidang akademik, nonakademik, dan keagamaan.
            </p>
        </div>

        @php
            $ikonKategori = [
                'akademik' => 'bi-mortarboard-fill',
                'nonakademik' => 'bi-trophy-fill',
                'keagamaan' => 'bi-moon-stars-fill',
            ];
        @endphp

        <div class="accordion" id="accordionPrestasi">
            @foreach($kategoriPrestasi as $key => $label)
                @php($items = $prestasisPerKategori->get($key, collect()))
                <div class="accordion-item border-0 rounded-4 shadow-sm mb-3 overflow-hidden">
                    <h2 class="accordion-header" id="heading-{{ $key }}">
                        <button class="accordion-button collapsed gap-3 px-4 py-4"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#kategori-{{ $key }}"
                                aria-expanded="false"
                                aria-controls="kategori-{{ $key }}">
                            <span class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                  style="width: 46px; height: 46px; background: #f1f5f9; border: 1px solid #cbd5e1;">
                                <img src="{{ asset('images/icon-prestasi.png') }}" alt="" style="width: 24px; height: 24px; object-fit: contain;">
                            </span>
                            <span>
                                <span class="d-block fw-bold fs-5 text-dark">{{ $label }}</span>
                                <span class="d-block small text-secondary fw-normal mt-1">
                                    {{ $items->count() }} catatan prestasi
                                </span>
                            </span>
                        </button>
                    </h2>

                    <div id="kategori-{{ $key }}"
                         class="accordion-collapse collapse"
                         aria-labelledby="heading-{{ $key }}"
                         data-bs-parent="#accordionPrestasi">
                        <div class="accordion-body p-4 border-top">
                            <div class="row g-4">
                                @forelse($items as $prestasi)
                                    <div class="col-md-6 col-lg-4">
                                        <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                                            @if($prestasi->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($prestasi->gambar))
                                                <img src="{{ asset('storage/' . $prestasi->gambar) }}"
                                                     class="card-img-top w-100 border-bottom"
                                                     style="height: 230px; object-fit: cover;"
                                                     alt="{{ $prestasi->judul }}">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center border-bottom bg-secondary bg-opacity-10"
                                                     style="height: 230px; padding: 20px;">
                                                    <img src="{{ asset('images/icon-prestasi.png') }}" alt="Prestasi" style="height: 90px; width: 90px; object-fit: contain; opacity: 0.35;">
                                                </div>
                                            @endif

                                            <div class="card-body p-4">
                                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                                    <span class="badge rounded-pill px-3 py-2" style="background: #e8eefc; color: #172554;">
                                                        {{ $label }}
                                                    </span>
                                                    <span class="small text-secondary">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        {{ $prestasi->tanggal->translatedFormat('d M Y') }}
                                                    </span>
                                                </div>
                                                <h5 class="card-title fw-bold text-dark mb-3" style="line-height: 1.4;">
                                                    {{ $prestasi->judul }}
                                                </h5>
                                                <dl class="mb-0 small">
                                                    <div class="d-flex gap-2 mb-2">
                                                        <dt class="text-secondary flex-shrink-0" style="width: 105px;">Nama Siswa</dt>
                                                        <dd class="fw-semibold text-dark mb-0">{{ $prestasi->nama_siswa ?: '-' }}</dd>
                                                    </div>
                                                    <div class="d-flex gap-2 mb-2">
                                                        <dt class="text-secondary flex-shrink-0" style="width: 105px;">Prestasi</dt>
                                                        <dd class="fw-bold mb-0" style="color: #172554;">{{ $prestasi->prestasi_medali ?: '-' }}</dd>
                                                    </div>
                                                    <div class="d-flex gap-2 mb-2">
                                                        <dt class="text-secondary flex-shrink-0" style="width: 105px;">Penyelenggara</dt>
                                                        <dd class="text-dark mb-0">{{ $prestasi->penyelenggara ?: '-' }}</dd>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <dt class="text-secondary flex-shrink-0" style="width: 105px;">Tingkat</dt>
                                                        <dd class="text-dark mb-0">{{ $prestasi->deskripsi }}</dd>
                                                    </div>
                                                </dl>
                                            </div>
                                        </article>
                                    </div>
                                @empty
                                    <div class="col-12">
                                         <div class="text-center rounded-4 border py-5 px-3 bg-light">
                                             <img src="{{ asset('images/icon-prestasi.png') }}" alt="Prestasi" style="height: 64px; width: 64px; object-fit: contain; opacity: 0.25;" class="mb-3 d-inline-block">
                                             <p class="text-secondary mb-0">Belum ada prestasi dalam kategori {{ strtolower($label) }}.</p>
                                         </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.location.hash.startsWith('#kategori-')) {
            return;
        }

        const target = document.querySelector(window.location.hash);
        if (!target || !target.classList.contains('accordion-collapse')) {
            return;
        }

        const item = target.closest('.accordion-item');
        target.addEventListener('shown.bs.collapse', function () {
            item?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, { once: true });
        bootstrap.Collapse.getOrCreateInstance(target, { toggle: false }).show();
    });
</script>
@endpush

@push('styles')
<style>
    .accordion-item {
        scroll-margin-top: 130px;
    }
</style>
@endpush
