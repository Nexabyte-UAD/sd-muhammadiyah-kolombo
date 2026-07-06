@extends('layouts.admin')

@section('title', 'Manajemen Prestasi')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data Prestasi Siswa</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.prestasi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Prestasi
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Lomba / Siswa</th>
                            <th>Prestasi / Medali</th>
                            <th>Penyelenggara</th>
                            <th>Kategori</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prestasis as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="Foto" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted border rounded"
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-trophy" title="Tanpa gambar"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-primary px-2 py-1">
                                    {{ \App\Models\Prestasi::KATEGORI[$item->kategori] ?? ucfirst($item->kategori) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <strong>{{ $item->judul }}</strong>
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-user mr-1"></i>{{ $item->nama_siswa ?: '-' }}
                                </div>
                            </td>
                            <td class="align-middle">{{ $item->prestasi_medali ?: '-' }}</td>
                            <td class="align-middle">{{ $item->penyelenggara ?: '-' }}</td>
                            <td class="align-middle">
                                <span class="badge badge-success px-2 py-1">
                                    {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td class="align-middle text-right">
                                <a href="{{ route('admin.prestasi.edit', $item->id) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.prestasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x d-block mb-3"></i>
                                Belum ada data prestasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($prestasis instanceof \Illuminate\Pagination\LengthAwarePaginator && $prestasis->hasPages())
            <div class="card-footer clearfix">
                {{ $prestasis->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
