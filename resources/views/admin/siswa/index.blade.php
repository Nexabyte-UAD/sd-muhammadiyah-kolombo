@extends('adminlte::page')

@section('title', 'Manajemen Siswa')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data Siswa & Alumni</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary mr-1">
                <i class="fas fa-plus mr-1"></i> Tambah Siswa
            </a>
            <a href="{{ route('admin.siswa.promote.page') }}" class="btn btn-success">
                <i class="fas fa-arrow-up mr-1"></i> Kenaikan Kelas Massal
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.siswa.index') }}" class="row align-items-center">
                    <!-- Status Filter -->
                    <div class="col-md-2 mb-2 mb-md-0">
                        <label class="small text-muted font-weight-bold">Status</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                            <option value="alumni" {{ $status === 'alumni' ? 'selected' : '' }}>Alumni</option>
                            <option value="keluar" {{ $status === 'keluar' ? 'selected' : '' }}>Pindah / Keluar</option>
                            <option value="arsip" {{ $status === 'arsip' ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>

                    <!-- Kelas Filter (Only for aktif status) -->
                    @if($status === 'aktif')
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="small text-muted font-weight-bold">Kelas</label>
                        <select name="kelas" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($daftarKelas as $itemKelas)
                                <option value="{{ $itemKelas->tingkat }}" @selected($kelas === $itemKelas->tingkat)>
                                    {{ $itemKelas->tingkat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Search Input -->
                    <div class="col-md-5 mb-2 mb-md-0">
                        <label class="small text-muted font-weight-bold">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIS..." value="{{ $search }}">
                            <div class="input-group-append">
                                <button class="btn btn-default" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Filters -->
                    <div class="col-md-2 mt-md-4 text-md-right">
                        <a href="{{ route('admin.siswa.index', ['status' => $status]) }}" class="btn btn-outline-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="mb-3 text-right">
            <a href="{{ route('admin.siswa.export', ['status' => $status]) }}" class="btn btn-success">
                <i class="fas fa-file-csv mr-1"></i> Ekspor CSV
            </a>
        </div>

        <!-- Data Card -->
        <div class="card card-primary card-outline">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Foto</th>
                            <th>Nama Lengkap</th>
                            <th>NIS</th>
                            <th>L/P</th>
                            <th>TTL</th>
                            <th>{{ $status === 'aktif' ? 'Kelas' : ($status === 'alumni' ? 'Tahun Lulus' : 'Status') }}</th>
                            <th>Tahun Masuk</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="img-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white img-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-weight: bold; display: inline-flex !important;">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold">{{ $item->nama }}</span>
                            </td>
                            <td class="align-middle">
                                {{ $item->nis ?? '-' }}
                            </td>
                            <td class="align-middle">
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
                                    <span class="badge badge-secondary px-2 py-1">Pindah / Keluar</span>
                                    @if($item->sekolah_tujuan)
                                        <div class="small text-muted mt-1">{{ $item->sekolah_tujuan }}</div>
                                    @endif
                                @else
                                    <span class="badge badge-dark px-2 py-1">Diarsipkan</span>
                                @endif
                            </td>
                            <td class="align-middle">{{ $item->tahun_masuk }}</td>
                            <td class="align-middle text-right">
                                @if($status === 'arsip')
                                    <form action="{{ route('admin.siswa.restore', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Pulihkan">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.siswa.edit', $item->id) }}" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Arsipkan data siswa ini?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Arsipkan">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-user-graduate fa-3x d-block mb-3"></i>
                                Belum ada data siswa ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($siswas->hasPages())
            <div class="card-footer clearfix">
                {{ $siswas->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
