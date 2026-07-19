{{--
    Halaman Direktori Struktural Guru & Staf Publik (pages/guru.blade.php)
    Menampilkan daftar seluruh Guru atau Staf sekolah secara responsif berbentuk grid kartu,
    dan memunculkan pop-up modal detail biodata lengkap (guru-staff-modal) ketika kartu diklik.
--}}
@extends('layouts.public')

@section('content')
<x-breadcrumb>Struktural: {{ ucfirst($tipe) }}</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-dark mb-2" style="font-size: 1.75rem;">
                {{ $tipe === 'guru' ? 'Guru' : 'Staf' }}
            </h2>
            <p class="text-secondary mb-0">
                {{ $tipe === 'guru'
                    ? 'Daftar tenaga pendidik SD Muhammadiyah Komplek Kolombo.'
                    : 'Daftar tenaga kependidikan SD Muhammadiyah Komplek Kolombo.' }}
            </p>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse($gurus as $guru)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white text-center"
                     role="button" tabindex="0" data-biodata-trigger data-bs-toggle="modal"
                     data-bs-target="#biodataTenaga-{{ $guru->id }}"
                     aria-label="Lihat biodata {{ $guru->nama }}">
                    @if($guru->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($guru->foto))
                        <img src="{{ asset('storage/' . $guru->foto) }}"
                             class="card-img-top w-100 border-bottom structural-photo"
                             alt="{{ $guru->nama }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center text-secondary w-100 structural-photo">
                            <x-admin-icon name="person-circle" size="112" class="default-profile-icon opacity-25"/>
                        </div>
                    @endif
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-1" style="line-height: 1.4;">{{ $guru->nama }}</h5>
                        <div class="text-success fw-bold mb-3 small">{{ $guru->jabatan }}</div>
                        <p class="text-secondary small mb-1">NIP: {{ $guru->nip ?: '-' }}</p>
                        @if($guru->bidang_tugas)
                            <p class="text-dark small mb-0 fw-medium">Bidang Tugas: {{ $guru->bidang_tugas }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <h5 class="text-secondary">Belum ada data tenaga pengajar.</h5>
            </div>
            @endforelse
        </div>

        @if($gurus->hasPages())
            <div class="d-flex justify-content-center mt-5 structural-pagination">
                {{ $gurus->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
</section>

@foreach($gurus as $guru)
    <x-guru-staff-modal :tenaga="$guru" />
@endforeach
@endsection

@push('styles')
<style>
    .structural-photo {
        aspect-ratio: 2 / 3;
        height: auto;
        object-fit: cover;
        object-position: center top;
    }

    .structural-pagination .pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
    }

    .structural-pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        padding: 0;
        border: 0;
        border-radius: 50% !important;
        color: #475569;
        background: transparent;
        font-weight: 600;
    }

    .structural-pagination .page-item.active .page-link {
        color: #fff;
        background: #172554;
        box-shadow: 0 4px 10px rgba(23, 37, 84, .2);
    }

    .structural-pagination .page-item:not(.active):not(.disabled) .page-link:hover {
        color: #0f172a;
        background: #f1f5f9;
    }

    .structural-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background: transparent;
    }

    .structural-pagination p.small.text-muted {
        display: none !important;
    }

    @media (max-width: 575.98px) {
        .structural-pagination nav > div:first-child .page-link {
            width: auto;
            padding: 0 .9rem;
            border-radius: 999px !important;
        }
    }
</style>
@endpush
