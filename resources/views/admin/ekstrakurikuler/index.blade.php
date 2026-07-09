@extends('layouts.admin')

@section('title', 'Manajemen Ekstrakurikuler')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data Ekstrakurikuler</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.ekstrakurikuler.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Ekstra
            </a>
        </div>
    </div>
@stop

@section('content')
<x-admin-usage-guide
    description="Petunjuk pengelolaan program ekstrakurikuler sekolah."
    :items="[
        'Tambahkan satu program untuk setiap kegiatan ekstrakurikuler.',
        'Lengkapi nama, jadwal, pembina, deskripsi, dan foto kegiatan.',
        'Data tersimpan digunakan pada website publik dan pilihan ekstrakurikuler siswa.',
    ]"
/>
<div class="row">
    <div class="col-12">
        <div class="card card-accent">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Kegiatan</th>
                            <th>Jadwal</th>
                            <th>Pembina</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ekstrakurikulers as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <strong>{{ $item->nama }}</strong>
                                <div class="text-muted small mt-1">
                                    {{ Str::limit($item->deskripsi, 40) }}
                                </div>
                            </td>
                            <td class="align-middle text-muted">
                                <i class="fas fa-clock mr-1"></i> {{ $item->jadwal }}
                            </td>
                            <td class="align-middle text-muted">
                                <i class="fas fa-user mr-1"></i> {{ $item->pembina ?? '-' }}
                            </td>
                            <td class="align-middle text-right">
                                <a href="{{ route('admin.ekstrakurikuler.edit', $item->id) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.ekstrakurikuler.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="d-inline">
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x d-block mb-3"></i>
                                Belum ada kegiatan ekstrakurikuler.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ekstrakurikulers instanceof \Illuminate\Pagination\LengthAwarePaginator && $ekstrakurikulers->hasPages())
            <div class="card-footer clearfix">
                {{ $ekstrakurikulers->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
