@extends('adminlte::page')

@section('title', 'Manajemen Berita')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data Publikasi & Berita</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.berita.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tulis Berita
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
                            <th>Judul Berita</th>
                            <th>Tanggal Rilis</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($beritas as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="gambar" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 60px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle" style="white-space: normal; min-width: 250px;">
                                <strong>{{ $item->judul }}</strong>
                                <div class="text-muted small mt-1">
                                    {{ Str::limit(strip_tags($item->isi), 80) }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info px-2 py-1">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                </span>
                            </td>
                            <td class="align-middle text-right">
                                <a href="{{ route('admin.berita.edit', $item->id) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.berita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="d-inline">
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
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x d-block mb-3"></i>
                                Belum ada data berita.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($beritas instanceof \Illuminate\Pagination\LengthAwarePaginator && $beritas->hasPages())
            <div class="card-footer clearfix">
                {{ $beritas->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
