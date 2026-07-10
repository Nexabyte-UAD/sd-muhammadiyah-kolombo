@extends('layouts.admin')

@section('title', 'Manajemen Siswa')
@section('page_kicker', 'Akademik')
@section('page_title', 'Data Siswa & Alumni')
@section('page_description', 'Kelola data siswa aktif, alumni, keluar, dan arsip.')

@section('page_actions')
    <a href="{{ route('admin.siswa.export', ['status' => $status]) }}" class="btn-admin btn-admin-secondary">
        <i class="fas fa-file-csv mr-1"></i> Ekspor CSV
    </a>
    <a href="{{ route('admin.siswa.promote.page') }}" class="btn-admin btn-admin-secondary">
        <i class="fas fa-arrow-up mr-1"></i> Kenaikan Kelas
    </a>
    <a href="{{ route('admin.siswa.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah Siswa
    </a>
@endsection

@section('content')
    <x-admin-usage-guide
        description="Petunjuk pengelolaan data siswa aktif, alumni, keluar, dan arsip."
        :items="[
            'Gunakan filter status dan kelas untuk menemukan siswa dengan cepat.',
            'Tambah atau edit siswa dengan data kelas dan identitas yang benar.',
            'Gunakan Kenaikan Kelas Massal hanya setelah keputusan setiap siswa diperiksa.',
            'Data yang dihapus masuk arsip dan dapat dipulihkan kembali.',
        ]"
    />

    <section class="admin-card">
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar Siswa</h2>
                <div class="admin-card-subtitle">{{ $siswas->total() }} siswa tersimpan</div>
            </div>
            <form method="GET" action="{{ route('admin.siswa.index') }}" class="admin-card-search" aria-label="Cari siswa">
                <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
                </select>

                <select name="status" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                    <option value="alumni" {{ $status === 'alumni' ? 'selected' : '' }}>Alumni</option>
                    <option value="keluar" {{ $status === 'keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="arsip" {{ $status === 'arsip' ? 'selected' : '' }}>Arsip</option>
                </select>

                @if($status === 'aktif')
                    <select name="kelas" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($daftarKelas as $itemKelas)
                            <option value="{{ $itemKelas->tingkat }}" @selected($kelas === $itemKelas->tingkat)>
                                Kelas {{ $itemKelas->tingkat }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <label class="data-search" for="search-input">
                    <i class="fas fa-search"></i>
                    <input type="search" id="search-input" name="search" value="{{ $search }}" placeholder="Cari nama atau NIS...">
                </label>
                <button type="submit" class="data-filter-submit">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                @if($search !== '' || $kelas !== '' || $status !== 'aktif' || $perPage != 10)
                    <a href="{{ route('admin.siswa.index', ['status' => $status]) }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="table-responsive">
            <table class="table table-hover admin-compact-table siswa-table" style="min-width: 950px;">
                <thead>
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th style="white-space: nowrap;">Nama Lengkap</th>
                        <th style="width: 110px;">NIS</th>
                        <th style="width: 80px;" class="text-center">L/P</th>
                        <th>TTL</th>
                        <th style="width: 130px;">{{ $status === 'aktif' ? 'Kelas' : ($status === 'alumni' ? 'Tahun Lulus' : 'Status') }}</th>
                        <th style="width: 120px;">Tahun Masuk</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px; border-radius: 8px; background: #f8fafc; border-color: #cbd5e1; display: inline-flex !important;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.65;">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->nama }}">
                                    <strong class="text-navy">{{ $item->nama }}</strong>
                                </div>
                            </td>
                            <td class="align-middle">{{ $item->nis ?? '-' }}</td>
                            <td class="align-middle text-center">
                                <span class="badge {{ $item->jenis_kelamin === 'L' ? 'badge-primary' : 'badge-danger' }} px-2 py-1">
                                    {{ $item->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($item->tempat_lahir || $item->tanggal_lahir)
                                    {{ $item->tempat_lahir ?? '-' }}, {{ $item->tanggal_lahir ? $item->tanggal_lahir->translatedFormat('d M Y') : '-' }}
                                @else
                                    -
                                  @endif
                            </td>
                            <td class="align-middle">
                                @if($status === 'aktif')
                                    <span class="badge badge-info px-2 py-1">{{ $item->kelasData?->tingkat ?? $item->kelas ?? '-' }}</span>
                                @elseif($status === 'alumni')
                                    <span class="badge badge-success px-2 py-1">Lulus {{ $item->tahun_lulus }}</span>
                                @elseif($status === 'keluar')
                                    <span class="badge badge-secondary px-2 py-1">Keluar</span>
                                    @if($item->sekolah_tujuan)
                                        <div class="small text-muted mt-1">{{ $item->sekolah_tujuan }}</div>
                                    @endif
                                @else
                                    <span class="badge badge-dark px-2 py-1">Diarsipkan</span>
                                @endif
                            </td>
                            <td class="align-middle">{{ $item->tahun_masuk }}</td>
                            <td class="align-middle text-center">
                                <div class="table-actions">
                                    @if($status === 'arsip')
                                        <form action="{{ route('admin.siswa.restore', $item->id) }}" method="POST" class="restore-form" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan siswa ini kembali menjadi status aktif?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-button" title="Pulihkan">
                                                Pulihkan
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.siswa.edit', $item->id) }}" class="action-button" title="Edit">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Arsipkan data siswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-button action-danger" title="Arsipkan">
                                                Arsip
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-user-graduate fa-3x d-block mb-3" style="color: #b4bdc9;"></i>
                                Belum ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siswas->hasPages())
            <footer class="admin-card-footer">
                <span>Halaman {{ $siswas->currentPage() }} dari {{ $siswas->lastPage() }}</span>
                <div class="pager">
                    @if($siswas->onFirstPage())
                        <span class="pager-link disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $siswas->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                    @endif

                    @for ($i = 1; $i <= $siswas->lastPage(); $i++)
                        @if ($i == $siswas->currentPage())
                            <span class="pager-link active">{{ $i }}</span>
                        @else
                            <a href="{{ $siswas->url($i) }}" class="pager-link">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($siswas->hasMorePages())
                        <a href="{{ $siswas->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                    @else
                        <span class="pager-link disabled">Berikutnya</span>
                    @endif
                </div>
            </footer>
        @endif
    </section>
@stop
