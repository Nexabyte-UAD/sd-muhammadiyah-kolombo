@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-dark">Data Kelas</h1>
        <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Tambah Kelas
        </a>
    </div>
@stop

@section('content')
<div class="card card-primary card-outline">
    <div class="card-body p-0 table-responsive">
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
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $item)
                    <tr>
                        <td class="align-middle text-center">{{ $kelas->firstItem() + $loop->index }}</td>
                        <td class="align-middle font-weight-bold">{{ $item->tingkat }}</td>
                        <td class="align-middle">{{ $item->jurusan ?: '-' }}</td>
                        <td class="align-middle">{{ $item->waliKelas?->nama ?? '-' }}</td>
                        <td class="align-middle">
                            {{ $item->siswas_count }} / {{ $item->kapasitas ?: '∞' }}
                        </td>
                        <td class="align-middle text-right">
                            <a href="{{ route('admin.kelas.edit', $item) }}" class="btn btn-sm btn-info" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.kelas.destroy', $item) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus data kelas ini?')">
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
                        <td colspan="6" class="text-center text-muted py-5">
                            Belum ada data kelas. Klik “Tambah Kelas” untuk mulai mengisi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kelas->hasPages())
        <div class="card-footer">{{ $kelas->links('pagination::bootstrap-4') }}</div>
    @endif
</div>
@stop
