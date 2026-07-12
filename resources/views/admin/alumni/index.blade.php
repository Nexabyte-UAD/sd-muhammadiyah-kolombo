{{--
    Halaman Direktori Alumni Admin (admin/alumni/index.blade.php)
    Menampilkan data alumni lulusan sekolah yang terintegrasi dengan data tracer study
    (pendidikan lanjutan), lengkap dengan filter angkatan/tahun kelulusan,
    pencarian nama/NIS, ekspor data CSV, serta aksi edit data atau hapus alumni.
--}}
@extends('layouts.admin')

@section('title', 'Data Alumni')
@section('page_kicker', 'Akademik · Alumni')
@section('page_title', 'Data Alumni')
@section('page_description', 'Kelola data lulusan dan lacak pendidikan lanjutan alumni.')

@section('page_actions')
    <a href="{{ route('admin.alumni.export', ['tahun_lulus' => $tahunLulus]) }}" class="btn-admin btn-admin-secondary">
        <x-admin-icon name="csv" size="18"/>
        Ekspor CSV
    </a>
@endsection

@section('content')
    <x-admin-usage-guide
        description="Panduan pengelolaan data alumni SD Muhammadiyah Kolombo."
        :items="[
            'Alumni otomatis tercatat saat siswa kelas 6 diluluskan melalui Kenaikan Kelas.',
            'Gunakan filter tahun lulus untuk menemukan alumni berdasarkan angkatan.',
            'Tambahkan riwayat pendidikan lanjutan (SMP, SMA, dst.) melalui menu Edit.',
            'Data yang dihapus masuk arsip dan dapat dipulihkan kembali.',
        ]"
    />

    <section class="admin-card">
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar Alumni</h2>
                <div class="admin-card-subtitle">{{ $alumni->total() }} alumni tersimpan</div>
            </div>
            <form method="GET" action="{{ route('admin.alumni.index') }}" class="admin-card-search" aria-label="Cari alumni">
                <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
                </select>

                <select name="tahun_lulus" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="">Semua Angkatan</option>
                    @foreach($daftarTahunLulus as $tahun)
                        <option value="{{ $tahun }}" @selected($tahunLulus == $tahun)>
                            Lulus {{ $tahun }}
                        </option>
                    @endforeach
                </select>

                <label class="data-search" for="search-alumni">
                    <x-admin-icon name="search" size="15"/>
                    <input type="search" id="search-alumni" name="search" value="{{ $search }}" placeholder="Cari nama atau NIS...">
                </label>
                <button type="submit" class="data-filter-submit">
                    <x-admin-icon name="search" size="15"/>
                    <span>Cari</span>
                </button>
                @if($search || $tahunLulus || $perPage != 10)
                    <a href="{{ route('admin.alumni.index') }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="table-responsive">
            <table class="table table-hover admin-compact-table" style="min-width: 850px;">
                <thead>
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th style="white-space: nowrap;">Nama Lengkap</th>
                        <th style="width: 110px;">NIS</th>
                        <th style="width: 80px;" class="text-center">L/P</th>
                        <th style="width: 120px;">Tahun Lulus</th>
                        <th>Pendidikan Lanjutan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alumni as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px; border-radius: 8px; background: #f8fafc; border-color: #cbd5e1; display: inline-flex !important;">
                                        <x-admin-icon name="user" size="20" style="opacity: 0.65;"/>
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
                                <span class="badge badge-success px-2 py-1">Lulus {{ $item->tahun_lulus }}</span>
                            </td>
                            <td class="align-middle">
                                @if($item->riwayatPendidikan->isNotEmpty())
                                    @php($pendidikan = $item->riwayatPendidikan->first())
                                    <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $pendidikan->jenjang }} — {{ $pendidikan->institusi }}">
                                        <strong>{{ $pendidikan->jenjang }}</strong> · {{ $pendidikan->institusi }}
                                    </div>
                                    @if($item->riwayatPendidikan->count() > 1)
                                        <div class="small text-muted">+{{ $item->riwayatPendidikan->count() - 1 }} lainnya</div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <div class="table-actions">
                                    <a href="{{ route('admin.siswa.edit', $item->id) }}" class="action-button" title="Edit">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Arsipkan data alumni ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-button action-danger" title="Arsipkan">
                                            Arsip
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <x-admin-icon name="graduation" size="48" style="opacity: 0.3; display: block; margin: 0 auto 12px;"/>
                                Belum ada data alumni ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alumni->hasPages())
            <footer class="admin-card-footer">
                <span>Halaman {{ $alumni->currentPage() }} dari {{ $alumni->lastPage() }}</span>
                <div class="pager">
                    @if($alumni->onFirstPage())
                        <span class="pager-link disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $alumni->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                    @endif

                    @for ($i = 1; $i <= $alumni->lastPage(); $i++)
                        @if ($i == $alumni->currentPage())
                            <span class="pager-link active">{{ $i }}</span>
                        @else
                            <a href="{{ $alumni->url($i) }}" class="pager-link">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($alumni->hasMorePages())
                        <a href="{{ $alumni->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                    @else
                        <span class="pager-link disabled">Berikutnya</span>
                    @endif
                </div>
            </footer>
        @endif
    </section>
@stop
