@extends('layouts.admin')

@section('title', 'Manajemen Prestasi')
@section('page_title', 'Data Prestasi Siswa')
@section('page_description', 'Kelola catatan prestasi siswa, kategori, penyelenggara, tanggal, dan dokumentasi yang tampil di halaman publik.')

@section('content')
<x-admin-usage-guide
    description="Petunjuk pencatatan prestasi siswa berdasarkan kategori."
    :items="[
        'Tambahkan prestasi pada kategori akademik, nonakademik, atau keagamaan yang tepat.',
        'Isi nama siswa, pencapaian, penyelenggara, tingkat, tanggal, dan dokumentasi secara lengkap.',
        'Perubahan data di halaman ini langsung memengaruhi halaman Prestasi dan Penghargaan publik.',
    ]"
/>

<section class="data-table-panel prestasi-data-panel">
    <form method="GET" action="{{ route('admin.prestasi.index') }}" class="data-table-toolbar">
        <div class="data-table-controls">
            <label class="data-search" aria-label="Cari prestasi">
                <i class="fas fa-search"></i>
                <input type="search" name="search" value="{{ $search }}" placeholder="Cari prestasi, siswa, penyelenggara...">
            </label>

            <label class="data-filter" aria-label="Filter kategori">
                <i class="fas fa-filter"></i>
                <select name="kategori" onchange="this.form.submit()">
                    <option value="">Semua kategori</option>
                    @foreach($kategoriPrestasi as $value => $label)
                        <option value="{{ $value }}" @selected($kategori === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            @if($search !== '' || $kategori !== '')
                <a href="{{ route('admin.prestasi.index') }}" class="data-reset">Reset</a>
            @endif
        </div>

        <button type="submit" class="data-filter-submit">
            <i class="fas fa-search"></i>
            <span>Cari</span>
        </button>

        <a href="{{ route('admin.prestasi.create') }}" class="btn-admin data-add-button">
            <i class="fas fa-plus"></i>
            <span>Tambah Prestasi</span>
        </a>
    </form>

    <div class="data-table-scroll">
        <table class="clean-data-table prestasi-admin-table">
            <colgroup>
                <col class="col-image">
                <col class="col-detail">
                <col class="col-result">
                <col class="col-organizer">
                <col class="col-category">
                <col class="col-date">
                <col class="col-actions">
            </colgroup>
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Lomba / Siswa</th>
                    <th>Prestasi / Medali</th>
                    <th>Penyelenggara</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th class="table-actions-heading">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prestasis as $item)
                    <tr>
                        <td>
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="Foto {{ $item->judul }}" class="prestasi-thumb">
                            @else
                                <div class="prestasi-thumb prestasi-thumb-empty">
                                    <i class="fas fa-trophy" title="Tanpa gambar"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong class="prestasi-title">{{ $item->judul }}</strong>
                            <div class="prestasi-student">
                                <i class="fas fa-user"></i>
                                <span>{{ $item->nama_siswa ?: '-' }}</span>
                            </div>
                        </td>
                        <td class="prestasi-result">{{ $item->prestasi_medali ?: '-' }}</td>
                        <td class="prestasi-muted">{{ $item->penyelenggara ?: '-' }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $kategoriPrestasi[$item->kategori] ?? ucfirst($item->kategori) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-success">
                                {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.prestasi.edit', $item->id) }}" class="action-button" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.prestasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button action-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="data-empty">
                            <i class="fas fa-folder-open"></i>
                            <span>Belum ada data prestasi.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($prestasis instanceof \Illuminate\Pagination\LengthAwarePaginator && $prestasis->hasPages())
        <div class="data-table-footer">
            {{ $prestasis->links('pagination::bootstrap-4') }}
        </div>
    @endif
</section>
@stop
