@extends('layouts.admin')

@section('title', 'Manajemen Prestasi')
@section('page_kicker', 'Konten website')
@section('page_title', 'Data Prestasi Siswa')
@section('page_description', 'Kelola catatan prestasi siswa, kategori, penyelenggara, tanggal, dan dokumentasi.')

@section('page_actions')
    <a href="{{ route('admin.prestasi.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah Prestasi
    </a>
@endsection

@section('content')
<x-admin-usage-guide
    description="Petunjuk pencatatan prestasi siswa berdasarkan kategori."
    :items="[
        'Tambahkan prestasi pada kategori akademik, nonakademik, atau keagamaan yang tepat.',
        'Isi nama siswa, pencapaian, penyelenggara, tingkat, tanggal, dan dokumentasi secara lengkap.',
        'Perubahan data di halaman ini langsung memengaruhi halaman Prestasi dan Penghargaan publik.',
    ]"
/>

<section class="admin-card">
    <header class="admin-card-header admin-card-header-with-search">
        <div>
            <h2 class="admin-card-title">Daftar Prestasi</h2>
            <div class="admin-card-subtitle">{{ $prestasis->total() }} prestasi terdaftar</div>
        </div>
        <form method="GET" action="{{ route('admin.prestasi.index') }}" class="admin-card-search" aria-label="Cari prestasi">
            <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>

            <select name="kategori" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($kategoriPrestasi as $value => $label)
                    <option value="{{ $value }}" @selected($kategori === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <label class="data-search" for="search-input">
                <i class="fas fa-search"></i>
                <input type="search" id="search-input" name="search" value="{{ $search }}" placeholder="Cari prestasi, siswa, penyelenggara...">
            </label>
            <button type="submit" class="data-filter-submit">
                <i class="fas fa-search"></i>
                <span>Cari</span>
            </button>
            @if($search !== '' || $kategori !== '' || $perPage != 10)
                <a href="{{ route('admin.prestasi.index') }}" class="data-reset">Reset</a>
            @endif
        </form>
    </header>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">Gambar</th>
                    <th>Nama Lomba / Siswa</th>
                    <th>Prestasi</th>
                    <th>Penyelenggara</th>
                    <th style="width: 140px;">Kategori</th>
                    <th style="width: 130px;">Tanggal</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prestasis as $item)
                    <tr>
                        <td class="align-middle">
                            <div class="content-thumb content-thumb-lg">
                                @if($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="Foto {{ $item->judul }}">
                                @else
                                    <img src="{{ asset('images/icon-prestasi.png') }}" alt="" style="width: 26px; height: 26px; object-fit: contain; opacity: 0.5;">
                                @endif
                            </div>
                        </td>
                        <td class="align-middle">
                            <strong class="text-navy">{{ $item->judul }}</strong>
                            <div class="text-muted small mt-1">
                                <i class="fas fa-user mr-1"></i> {{ $item->nama_siswa ?: '-' }}
                            </div>
                        </td>
                        <td class="align-middle">
                            <div style="max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->prestasi_medali ?? '' }}">
                                {{ $item->prestasi_medali ?: '-' }}
                            </div>
                        </td>
                        <td class="align-middle">
                            <div style="max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->penyelenggara ?? '' }}">
                                {{ $item->penyelenggara ?: '-' }}
                            </div>
                        </td>
                        <td class="align-middle" style="white-space: nowrap;">
                            <span class="badge badge-primary px-2 py-1">
                                {{ $kategoriPrestasi[$item->kategori] ?? ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td class="align-middle" style="white-space: nowrap;">
                            <span class="badge badge-success px-2 py-1">
                                {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            <div class="table-actions">
                                <a href="{{ route('admin.prestasi.edit', $item->id) }}" class="action-button" title="Edit">
                                    Edit
                                </a>
                                <form action="{{ route('admin.prestasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" style="display: inline-block;">
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x d-block mb-3" style="color: #b4bdc9;"></i>
                            Belum ada data prestasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($prestasis->hasPages())
        <footer class="admin-card-footer">
            <span>Halaman {{ $prestasis->currentPage() }} dari {{ $prestasis->lastPage() }}</span>
            <div class="pager">
                @if($prestasis->onFirstPage())
                    <span class="pager-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $prestasis->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                @endif

                @for ($i = 1; $i <= $prestasis->lastPage(); $i++)
                    @if ($i == $prestasis->currentPage())
                        <span class="pager-link active">{{ $i }}</span>
                    @else
                        <a href="{{ $prestasis->url($i) }}" class="pager-link">{{ $i }}</a>
                    @endif
                @endfor

                @if($prestasis->hasMorePages())
                    <a href="{{ $prestasis->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                @else
                    <span class="pager-link disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    @endif
</section>
@stop
