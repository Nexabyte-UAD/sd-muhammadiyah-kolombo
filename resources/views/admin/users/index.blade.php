@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data User Admin</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah User
            </a>
        </div>
    </div>
@stop

@section('content')
<x-admin-usage-guide
    description="Petunjuk pengelolaan akun yang dapat mengakses panel admin."
    :items="[
        'Buat akun hanya untuk pengguna yang berwenang mengelola website.',
        'Gunakan alamat email aktif dan password kuat yang tidak dibagikan.',
        'Tinjau akun secara berkala dan hapus akses pengguna yang sudah tidak bertugas.',
    ]"
/>
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Terdaftar Pada</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $item)
                        <tr>
                            <td class="align-middle">
                                <strong>{{ $item->name }}</strong>
                            </td>
                            <td class="align-middle">
                                {{ $item->email }}
                            </td>
                            <td class="align-middle text-muted">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="align-middle text-right">
                                <a href="{{ route('admin.users.edit', $item->id) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="d-inline">
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
                                <i class="fas fa-users-slash fa-3x d-block mb-3"></i>
                                Belum ada data user.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
            <div class="card-footer clearfix">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
