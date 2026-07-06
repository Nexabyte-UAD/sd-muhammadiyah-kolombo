@extends('layouts.admin')

@section('title', 'Manajemen Pegawai')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data {{ ucfirst($tipe) }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.guru-staff.create', ['tipe' => $tipe]) }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah {{ ucfirst($tipe) }}
            </a>
        </div>
    </div>
@stop

@section('content')
<x-admin-usage-guide
    description="Petunjuk pengelolaan profil guru dan tenaga kependidikan."
    :items="[
        'Pastikan berada pada kategori Guru atau Staf yang sesuai.',
        'Lengkapi jabatan, bidang tugas, biodata, dan foto untuk tampilan publik.',
        'Gunakan Edit untuk memperbarui profil dan Hapus hanya untuk data yang tidak lagi digunakan.',
    ]"
/>
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Profil</th>
                            <th>Informasi Pegawai</th>
                            <th>Jabatan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gurus as $item)
                        <tr>
                            <td class="align-middle">
                                @if($item->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="img-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white img-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: bold; display: inline-flex !important;">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <strong>{{ $item->nama }}</strong>
                                <div class="text-muted small mt-1">
                                    NIP: {{ $item->nip ?: '-' }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-info px-2 py-1 mb-1">
                                    {{ $item->jabatan }}
                                </span>
                                @if($item->bidang_tugas)
                                <div class="small text-muted">Bidang Tugas: {{ $item->bidang_tugas }}</div>
                                @endif
                            </td>
                            <td class="align-middle text-right">
                                <a href="{{ route('admin.guru-staff.edit', $item->id) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.guru-staff.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="d-inline">
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
                                Belum ada data pegawai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($gurus instanceof \Illuminate\Pagination\LengthAwarePaginator && $gurus->hasPages())
            <div class="card-footer clearfix">
                {{ $gurus->appends(['tipe' => $tipe])->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
