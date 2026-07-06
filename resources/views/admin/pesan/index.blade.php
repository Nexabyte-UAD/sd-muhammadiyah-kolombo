@extends('layouts.admin')

@section('title', 'Kotak Masuk')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pesan & Masukan Pengunjung</h1>
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
                            <th>Pengirim</th>
                            <th>Kontak Email</th>
                            <th>Isi Pesan</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesans as $item)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white img-circle d-flex align-items-center justify-content-center mr-2" style="width: 40px; height: 40px; font-weight: bold; display: inline-flex !important;">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </div>
                                    <strong>{{ $item->nama }}</strong>
                                </div>
                            </td>
                            <td class="align-middle">
                                <a href="mailto:{{ $item->email }}" class="text-secondary">
                                    <i class="fas fa-envelope mr-1"></i> {{ $item->email }}
                                </a>
                            </td>
                            <td class="align-middle" style="white-space: normal; min-width: 250px;">
                                <div class="text-muted">
                                    "{{ $item->isi ?? $item->pesan }}"
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border">
                                    {{ $item->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td class="align-middle text-right">
                                <form action="{{ route('admin.pesan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pesan ini secara permanen?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-envelope-open-text fa-3x d-block mb-3"></i>
                                Kotak masuk kosong. Belum ada pesan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pesans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pesans->hasPages())
            <div class="card-footer clearfix">
                {{ $pesans->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop
