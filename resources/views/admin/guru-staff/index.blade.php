{{--
    Halaman Daftar Pegawai Guru & Staf (admin/guru-staff/index.blade.php)
    Menampilkan data pegawai sekolah yang terbagi atas kategori tipe "guru" atau "staf",
    lengkap dengan jabatan, NIP, foto profil, fitur pencarian, filter jumlah baris per halaman,
    serta aksi tambah, edit, dan hapus pegawai.
--}}
@extends('layouts.admin')

@section('title', 'Manajemen Pegawai')
@section('page_kicker', 'Konten website')
@section('page_title', 'Data ' . ucfirst($tipe))
@section('page_description', 'Kelola informasi profil pendidik dan tenaga kependidikan.')

@section('page_actions')
    <a href="{{ route('admin.guru-staff.create', ['tipe' => $tipe]) }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah {{ ucfirst($tipe) }}
    </a>
@endsection

@section('content')
<x-admin-usage-guide
    description="Petunjuk pengelolaan profil guru dan tenaga kependidikan."
    :items="[
        'Pastikan berada pada kategori Guru atau Staf yang sesuai.',
        'Lengkapi jabatan, bidang tugas, biodata, dan foto untuk tampilan publik.',
        'Gunakan Edit untuk memperbarui profil dan Hapus hanya untuk data yang tidak lagi digunakan.',
    ]"
/>

<section class="admin-card">
    <header class="admin-card-header admin-card-header-with-search">
        <div>
            <h2 class="admin-card-title">Daftar {{ ucfirst($tipe) }}</h2>
            <div class="admin-card-subtitle">{{ $gurus->total() }} data ditemukan</div>
        </div>
        <form method="GET" action="{{ route('admin.guru-staff.index') }}" class="admin-card-search" aria-label="Cari pegawai">
            <input type="hidden" name="tipe" value="{{ $tipe }}">
            <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
            <label class="data-search" for="search-input">
                <x-admin-icon name="search" size="15"/>
                <input type="search" id="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, NIP, jabatan...">
            </label>
            <button type="submit" class="data-filter-submit">
                <x-admin-icon name="search" size="15"/>
                <span>Cari</span>
            </button>
            @if(isset($search) && $search !== '')
                <a href="{{ route('admin.guru-staff.index', ['tipe' => $tipe]) }}" class="data-reset">Reset</a>
            @endif
        </form>
    </header>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">Profil</th>
                    <th>Informasi Pegawai</th>
                    <th>Jabatan</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gurus as $item)
                    <tr>
                        <td class="align-middle">
                            @if($item->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto))
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div class="bg-light text-secondary d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px; border-radius: 8px; background: #f8fafc; border-color: #cbd5e1;">
                                    <x-admin-icon name="user" size="24" style="opacity: 0.65;"/>
                                </div>
                            @endif
                        </td>
                        <td class="align-middle">
                            <strong class="text-navy">{{ $item->nama }}</strong>
                            <div class="text-muted small mt-1">
                                NIP: {{ $item->nip ?: '-' }}
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-info px-2 py-1 mb-1">
                                {{ $item->jabatan }}
                            </span>
                            @if($item->bidang_tugas)
                                <div class="small text-muted">Bidang Tugas: {{ $item->bidang_tugas }}</div>
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            <div class="table-actions">
                                <a href="{{ route('admin.guru-staff.edit', $item->id) }}" class="action-button" title="Edit">
                                    Edit
                                </a>
                                <form action="{{ route('admin.guru-staff.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button action-danger" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            @if(isset($search) && $search !== '')
                                <div class="empty-state">
                                    <strong>{{ ucfirst($tipe) }} tidak ditemukan</strong>
                                    <p>Tidak ada data {{ $tipe }} yang cocok dengan pencarian "{{ $search }}".</p>
                                    <a href="{{ route('admin.guru-staff.index', ['tipe' => $tipe]) }}" class="btn-admin">Tampilkan Semua</a>
                                </div>
                            @else
                                <x-admin-icon name="folder-open" size="48" style="color: #b4bdc9; display: block; margin: 0 auto 12px;"/>
                                Belum ada data pegawai.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($gurus->hasPages())
        <footer class="admin-card-footer">
            <span>Halaman {{ $gurus->currentPage() }} dari {{ $gurus->lastPage() }}</span>
            <div class="pager">
                @if($gurus->onFirstPage())
                    <span class="pager-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $gurus->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                @endif

                @for ($i = 1; $i <= $gurus->lastPage(); $i++)
                    @if ($i == $gurus->currentPage())
                        <span class="pager-link active">{{ $i }}</span>
                    @else
                        <a href="{{ $gurus->url($i) }}" class="pager-link">{{ $i }}</a>
                    @endif
                @endfor

                @if($gurus->hasMorePages())
                    <a href="{{ $gurus->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                @else
                    <span class="pager-link disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    @endif
</section>
@stop
