@extends('layouts.admin')

@section('title', 'Manajemen Ekstrakurikuler')
@section('page_kicker', 'Konten website')
@section('page_title', 'Data Ekstrakurikuler')
@section('page_description', 'Kelola informasi kegiatan ekstrakurikuler sekolah.')

@section('page_actions')
    <a href="{{ route('admin.ekstrakurikuler.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah Ekstra
    </a>
@endsection

@section('content')
<x-admin-usage-guide
    description="Petunjuk pengelolaan program ekstrakurikuler sekolah."
    :items="[
        'Tambahkan satu program untuk setiap kegiatan ekstrakurikuler.',
        'Lengkapi nama, jadwal, pembina, deskripsi, dan foto kegiatan.',
        'Data tersimpan digunakan pada website publik dan pilihan ekstrakurikuler siswa.',
    ]"
/>

<section class="admin-card">
    <header class="admin-card-header admin-card-header-with-search">
        <div>
            <h2 class="admin-card-title">Daftar Ekstrakurikuler</h2>
            <div class="admin-card-subtitle">{{ $ekstrakurikulers->total() }} ekstra terdaftar</div>
        </div>
        <form method="GET" action="{{ route('admin.ekstrakurikuler.index') }}" class="admin-card-search" aria-label="Cari ekstra">
            <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
            <label class="data-search">
                <i class="fas fa-search"></i>
                <input type="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, pembina, jadwal...">
            </label>
            <button type="submit" class="data-filter-submit">
                <i class="fas fa-search"></i>
                <span>Cari</span>
            </button>
            @if(isset($search) && $search !== '')
                <a href="{{ route('admin.ekstrakurikuler.index') }}" class="data-reset">Reset</a>
            @endif
        </form>
    </header>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 90px;">Gambar</th>
                    <th>Nama Kegiatan</th>
                    <th>Jadwal</th>
                    <th>Pembina</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ekstrakurikulers as $item)
                    <tr>
                        <td class="align-middle">
                            @if($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            @else
                                <div style="width: 50px; height: 50px; background: #f1f5f9; color: #94a3b8; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="align-middle">
                            <strong class="text-navy">{{ $item->nama }}</strong>
                            <div class="text-muted small mt-1">
                                {{ Str::limit($item->deskripsi, 55) }}
                            </div>
                        </td>
                        <td class="align-middle text-muted">
                            <i class="fas fa-clock mr-1" style="font-size: 12px;"></i> {{ $item->jadwal }}
                        </td>
                        <td class="align-middle text-muted">
                            <i class="fas fa-user mr-1" style="font-size: 12px;"></i> {{ $item->pembina ?? '-' }}
                        </td>
                        <td class="align-middle text-center">
                            <div class="table-actions">
                                <a href="{{ route('admin.ekstrakurikuler.edit', $item->id) }}" class="action-button" title="Edit">
                                    Edit
                                </a>
                                <form action="{{ route('admin.ekstrakurikuler.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            @if(isset($search) && $search !== '')
                                <div class="empty-state">
                                    <strong>Ekstrakurikuler tidak ditemukan</strong>
                                    <p>Tidak ada kegiatan ekstrakurikuler yang cocok dengan pencarian "{{ $search }}".</p>
                                    <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn-admin">Tampilkan Semua</a>
                                </div>
                            @else
                                <i class="fas fa-folder-open fa-3x d-block mb-3" style="color: #b4bdc9;"></i>
                                Belum ada kegiatan ekstrakurikuler.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ekstrakurikulers->hasPages())
        <footer class="admin-card-footer">
            <span>Halaman {{ $ekstrakurikulers->currentPage() }} dari {{ $ekstrakurikulers->lastPage() }}</span>
            <div class="pager">
                @if($ekstrakurikulers->onFirstPage())
                    <span class="pager-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $ekstrakurikulers->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                @endif

                @for ($i = 1; $i <= $ekstrakurikulers->lastPage(); $i++)
                    @if ($i == $ekstrakurikulers->currentPage())
                        <span class="pager-link active">{{ $i }}</span>
                    @else
                        <a href="{{ $ekstrakurikulers->url($i) }}" class="pager-link">{{ $i }}</a>
                    @endif
                @endfor

                @if($ekstrakurikulers->hasMorePages())
                    <a href="{{ $ekstrakurikulers->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                @else
                    <span class="pager-link disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    @endif
</section>
@stop
