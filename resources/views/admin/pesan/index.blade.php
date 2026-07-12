{{--
    Halaman Kotak Masuk Pesan Pengunjung (admin/pesan/index.blade.php)
    Menampilkan daftar pesan masuk dari pengunjung web beserta kontak email pengirim,
    status baca/belum dibaca, dan aksi untuk menandai sudah dibaca atau hapus pesan secara permanen.
--}}
@extends('layouts.admin')

@section('title', 'Kotak Masuk')
@section('page_kicker', 'Pengelolaan')
@section('page_title', 'Pesan & Masukan')
@section('page_description', 'Tinjau pesan, saran, dan masukan dari pengunjung website sekolah.')

@section('content')
<x-admin-usage-guide
    description="Petunjuk meninjau pesan dan masukan dari pengunjung website."
    :items="[
        'Pesan baru ditampilkan dengan latar lebih tegas sampai ditandai sudah dibaca.',
        'Gunakan alamat email pengirim untuk memberikan balasan bila diperlukan.',
        'Hapus permanen hanya pesan yang sudah selesai ditindaklanjuti atau tidak relevan.',
    ]"
/>

<section class="admin-card">
    <header class="admin-card-header">
        <div>
            <h2 class="admin-card-title">Pesan Masuk</h2>
            <div class="admin-card-subtitle">{{ $pesans->total() }} pesan diterima</div>
        </div>
        <form method="GET" action="{{ route('admin.pesan.index') }}" class="admin-card-search" aria-label="Filter pesan">
            <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
        </form>
    </header>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 200px;">Pengirim</th>
                    <th style="width: 220px;">Kontak Email</th>
                    <th>Isi Pesan</th>
                    <th style="width: 140px;">Tanggal</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesans as $item)
                    <tr class="{{ $item->read_at ? '' : 'font-weight-bold bg-light' }}">
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px; font-weight: bold; border-radius: 50%; font-size: 12px; display: inline-flex !important;">
                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                </div>
                                <span class="text-navy">{{ $item->nama }}</span>
                            </div>
                        </td>
                        <td class="align-middle">
                            <a href="mailto:{{ $item->email }}" class="text-secondary" style="text-decoration: none;">
                                <x-admin-icon name="envelope" size="13" class="mr-1"/>
                                {{ $item->email }}
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
                        <td class="align-middle text-center">
                            <div class="table-actions">
                                @if(!$item->read_at)
                                    <form action="{{ route('admin.pesan.read', $item) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-button" title="Tandai Sudah Dibaca">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @else
                                    <span class="badge badge-success px-2 py-1 mr-1">Dibaca</span>
                                @endif
                                <form action="{{ route('admin.pesan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pesan ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button action-danger" title="Hapus Permanen">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <x-admin-icon name="envelope-open" size="48" style="color: #b4bdc9; display: block; margin: 0 auto 12px;"/>
                            Kotak masuk kosong. Belum ada pesan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesans->hasPages())
        <footer class="admin-card-footer">
            <span>Halaman {{ $pesans->currentPage() }} dari {{ $pesans->lastPage() }}</span>
            <div class="pager">
                @if($pesans->onFirstPage())
                    <span class="pager-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $pesans->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                @endif

                @for ($i = 1; $i <= $pesans->lastPage(); $i++)
                    @if ($i == $pesans->currentPage())
                        <span class="pager-link active">{{ $i }}</span>
                    @else
                        <a href="{{ $pesans->url($i) }}" class="pager-link">{{ $i }}</a>
                    @endif
                @endfor

                @if($pesans->hasMorePages())
                    <a href="{{ $pesans->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                @else
                    <span class="pager-link disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    @endif
</section>
@stop
