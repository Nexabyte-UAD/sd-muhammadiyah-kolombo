@extends('layouts.admin')

@section('title', 'Berita')
@section('page_kicker', 'Konten website')
@section('page_title', 'Berita')
@section('page_description', 'Kelola informasi dan publikasi yang tampil pada website sekolah.')

@section('page_actions')
    <a href="{{ route('admin.berita.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tulis Berita
    </a>
@endsection

@section('content')
    <x-admin-usage-guide
        description="Petunjuk singkat pengelolaan publikasi berita sekolah."
        :items="[
            'Pilih Tulis Berita untuk membuat publikasi baru.',
            'Pastikan judul, tanggal, isi, dan gambar sudah sesuai sebelum disimpan.',
            'Gunakan Edit untuk memperbarui berita; Hapus hanya jika konten tidak lagi diperlukan.',
        ]"
    />

    <section class="admin-card">
        <header class="admin-card-header">
            <div>
                <h2 class="admin-card-title">Daftar Berita</h2>
                <div class="admin-card-subtitle">{{ $beritas->total() }} berita tersimpan</div>
            </div>
        </header>

        <div class="admin-card-body flush">
            @if($beritas->isNotEmpty())
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Berita</th>
                                <th>Tanggal Rilis</th>
                                <th>Status</th>
                                <th class="table-actions-heading">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($beritas as $item)
                                <tr>
                                    <td>
                                        <div class="content-cell">
                                            <div class="content-thumb content-thumb-lg">
                                                @if($item->gambar)
                                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="">
                                                @else
                                                    <x-admin-icon name="news" size="21"/>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="content-title">{{ $item->judul }}</div>
                                                <div class="content-meta">{{ Str::limit(strip_tags($item->isi), 78) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <span class="status-badge {{ $item->status === 'published' ? 'status-success' : 'status-muted' }}">
                                            {{ $item->status === 'published' ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="{{ route('admin.berita.edit', $item) }}" class="action-button" title="Edit berita">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.berita.destroy', $item) }}" method="POST"
                                                  onsubmit="return confirm('Hapus berita ini? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-button action-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <strong>Belum ada berita</strong>
                    <p>Mulai dengan menambahkan publikasi pertama sekolah.</p>
                    <a href="{{ route('admin.berita.create') }}" class="btn-admin">Tulis Berita</a>
                </div>
            @endif
        </div>

        @if($beritas->hasPages())
            <footer class="admin-card-footer">
                <span>Halaman {{ $beritas->currentPage() }} dari {{ $beritas->lastPage() }}</span>
                <div class="pager">
                    @if($beritas->onFirstPage())
                        <span class="pager-link disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $beritas->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                    @endif
                    @if($beritas->hasMorePages())
                        <a href="{{ $beritas->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                    @else
                        <span class="pager-link disabled">Berikutnya</span>
                    @endif
                </div>
            </footer>
        @endif
    </section>
@endsection
