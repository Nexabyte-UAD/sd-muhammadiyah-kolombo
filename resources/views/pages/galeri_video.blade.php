@extends('layouts.public')

@section('content')
<x-breadcrumb>Galeri Video</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Header & Filter -->
        <div class="mb-4 pb-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">Galeri Video</h2>
                <p class="text-secondary mb-0">
                    Dokumentasi video liputan dan kegiatan SD Muhammadiyah Komplek Kolombo.
                </p>
            </div>
            
            <form action="{{ route('galeri.video') }}" method="GET" class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                <select name="kategori" class="form-select form-select-sm border-secondary-subtle" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" class="form-control form-control-sm border-secondary-subtle" placeholder="Cari video..." value="{{ $search }}" style="width: 180px;">
            </form>
        </div>

        <!-- Grid Video -->
        <div class="row g-4">
            @forelse($videos as $video)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden gallery-video-card"
                         data-bs-toggle="modal"
                         data-bs-target="#videoModal"
                         data-embed="{{ $video->embed_url }}"
                         data-judul="{{ $video->judul }}"
                         data-kategori="{{ $video->kategori }}"
                         data-tanggal="{{ $video->tanggal ? $video->tanggal->translatedFormat('d F Y') : '' }}"
                         data-keterangan="{{ $video->keterangan }}">
                        <div class="position-relative overflow-hidden" style="height: 220px;">
                            <img src="{{ $video->thumbnail_url }}" class="w-100 h-100 gallery-video-img" style="object-fit: cover; transition: transform 0.4s ease;" alt="{{ $video->judul }}">
                            
                            <!-- Play Button Overlay -->
                            <div class="position-absolute top-50 start-50 translate-middle d-flex align-items-center justify-content-center play-btn-wrapper">
                                <div class="play-btn bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg">
                                    <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor"><path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/></svg>
                                </div>
                            </div>

                            <span class="badge bg-primary text-uppercase position-absolute top-0 start-0 m-3" style="font-size: 0.65rem; z-index: 2;">{{ $video->kategori }}</span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark mb-2 lh-base" style="font-size: 1.05rem;">
                                {{ Str::limit($video->judul, 60) }}
                            </h5>
                            @if($video->keterangan)
                                <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.6;">
                                    {{ Str::limit(strip_tags($video->keterangan), 85) }}
                                </p>
                            @endif
                            @if($video->tanggal)
                                <div class="mt-auto pt-2 border-top text-secondary small">
                                    <x-admin-icon name="clock" size="13" class="me-1"/>{{ $video->tanggal->translatedFormat('d F Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center rounded-4 border py-5 px-3 bg-light">
                        <x-admin-icon name="youtube" size="56" class="text-secondary opacity-25 mb-3"/>
                        <h5 class="fw-bold text-dark mb-2">Belum Ada Video</h5>
                        <p class="text-secondary mb-0">Dokumentasi video kegiatan sekolah akan ditampilkan di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <x-public-pagination :paginator="$videos" />
    </div>
</section>

<!-- Modal Player Video YouTube -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-dark text-white py-3 px-4">
                <div>
                    <span class="badge bg-primary text-uppercase mb-1" id="modalVideoCategory" style="font-size: 0.7rem;"></span>
                    <h5 class="modal-title fw-bold mb-0 text-white" id="modalVideoTitle"></h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0 bg-black text-center position-relative">
                <div class="ratio ratio-16x9">
                    <iframe src="" id="modalVideoIframe" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="Video Player"></iframe>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-between p-3">
                <span class="text-secondary small" id="modalVideoDate"></span>
                <div class="text-dark small mb-0 fw-semibold ck-content" id="modalVideoDesc"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .gallery-video-card {
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gallery-video-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12) !important;
    }
    .gallery-video-card:hover .gallery-video-img {
        transform: scale(1.05);
    }
    .play-btn {
        width: 54px;
        height: 54px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }
    .gallery-video-card:hover .play-btn {
        transform: scale(1.15);
        background-color: #dc2626 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const videoModal = document.getElementById('videoModal');
        const modalIframe = document.getElementById('modalVideoIframe');

        if (videoModal) {
            videoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const embed = button.getAttribute('data-embed');
                const judul = button.getAttribute('data-judul');
                const kategori = button.getAttribute('data-kategori');
                const tanggal = button.getAttribute('data-tanggal');
                const keterangan = button.getAttribute('data-keterangan');

                modalIframe.src = embed;
                document.getElementById('modalVideoTitle').textContent = judul;
                document.getElementById('modalVideoCategory').textContent = kategori;
                document.getElementById('modalVideoDate').textContent = tanggal ? 'Tanggal: ' + tanggal : '';
                document.getElementById('modalVideoDesc').innerHTML = keterangan || '';
            });

            videoModal.addEventListener('hide.bs.modal', function() {
                modalIframe.src = '';
            });
        }
    });
</script>
@endpush
