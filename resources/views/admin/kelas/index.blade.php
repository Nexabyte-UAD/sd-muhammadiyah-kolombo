@extends('layouts.admin')

@section('title', 'Data Kelas')
@section('page_kicker', 'Akademik')
@section('page_title', 'Data Kelas')
@section('page_description', 'Kelola kelompok kelas belajar, wali kelas, dan kapasitas siswa.')

@section('page_actions')
    <a href="{{ route('admin.kelas.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah Kelas
    </a>
@endsection

@section('content')
<x-admin-usage-guide
    description="Petunjuk pengelolaan kelas, kapasitas, dan wali kelas."
    :items="[
        'Buat kelas sesuai penamaan resmi yang digunakan sekolah.',
        'Pilih wali kelas dari data guru dan tentukan kapasitas bila diperlukan.',
        'Kelas yang masih digunakan siswa tidak dapat dihapus agar data tetap konsisten.',
    ]"
/>

<section class="admin-card">
    <header class="admin-card-header admin-card-header-with-search">
        <div>
            <h2 class="admin-card-title">Daftar Kelas</h2>
            <div class="admin-card-subtitle">{{ $kelas->total() }} kelas terdaftar</div>
        </div>
        <form method="GET" action="{{ route('admin.kelas.index') }}" class="admin-card-search" aria-label="Cari kelas">
            <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
            <label class="data-search" for="search-input">
                <i class="fas fa-search"></i>
                <input type="search" id="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Cari tingkat, jurusan, wali kelas...">
            </label>
            <button type="submit" class="data-filter-submit">
                <i class="fas fa-search"></i>
                <span>Cari</span>
            </button>
            @if(isset($search) && $search !== '')
                <a href="{{ route('admin.kelas.index') }}" class="data-reset">Reset</a>
            @endif
        </form>
    </header>

    <div class="table-responsive">
        @if($errors->any())
            <div class="alert alert-danger m-3">{{ $errors->first() }}</div>
        @endif
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width: 70px;">No</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th>Kapasitas</th>
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $item)
                    <tr>
                        <td class="align-middle text-center">{{ $kelas->firstItem() + $loop->index }}</td>
                        <td class="align-middle font-weight-bold text-navy">{{ $item->tingkat }}</td>
                        <td class="align-middle">{{ $item->jurusan ?: '-' }}</td>
                        <td class="align-middle">{{ $item->waliKelas?->nama ?? '-' }}</td>
                        <td class="align-middle">
                            {{ $item->siswas_count }} / {{ $item->kapasitas ?: '∞' }}
                        </td>
                        <td class="align-middle text-center">
                            <div class="table-actions">
                                <a href="{{ route('admin.kelas.edit', $item) }}" class="action-button" title="Edit">
                                    Edit
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Hapus data kelas ini?')">
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            @if(isset($search) && $search !== '')
                                <div class="empty-state">
                                    <strong>Kelas tidak ditemukan</strong>
                                    <p>Tidak ada data kelas yang cocok dengan pencarian "{{ $search }}".</p>
                                    <a href="{{ route('admin.kelas.index') }}" class="btn-admin">Tampilkan Semua</a>
                                </div>
                            @else
                                Belum ada data kelas. Klik “Tambah Kelas” untuk mulai mengisi.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kelas->hasPages())
        <footer class="admin-card-footer">
            <span>Halaman {{ $kelas->currentPage() }} dari {{ $kelas->lastPage() }}</span>
            <div class="pager">
                @if($kelas->onFirstPage())
                    <span class="pager-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $kelas->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                @endif

                @for ($i = 1; $i <= $kelas->lastPage(); $i++)
                    @if ($i == $kelas->currentPage())
                        <span class="pager-link active">{{ $i }}</span>
                    @else
                        <a href="{{ $kelas->url($i) }}" class="pager-link">{{ $i }}</a>
                    @endif
                @endfor

                @if($kelas->hasMorePages())
                    <a href="{{ $kelas->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                @else
                    <span class="pager-link disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    @endif
</section>
@stop
