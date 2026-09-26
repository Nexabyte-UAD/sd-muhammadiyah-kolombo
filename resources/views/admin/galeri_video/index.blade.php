@extends('layouts.admin')

@section('title', 'Galeri Video')
@section('page_kicker', 'Manajemen Konten')
@section('page_title', 'Galeri Video Sekolah')
@section('page_description', 'Kelola dokumentasi video kegiatan dan liputan SD Muhammadiyah Komplek Kolombo.')

@section('page_actions')
    <a href="{{ route('admin.galeri-video.create') }}" class="btn-admin primary">
        <x-admin-icon name="plus" size="18"/>
        <span>Tambah Video Baru</span>
    </a>
@endsection

@section('content')
    <section class="admin-card">
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar Video Galeri</h2>
                <div class="admin-card-subtitle">{{ $videos->total() }} video tersimpan</div>
            </div>
            
            <form method="GET" action="{{ route('admin.galeri-video.index') }}" class="admin-card-search" aria-label="Cari video">
                <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                </select>

                <select name="kategori" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" @selected(($kategori ?? '') === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>

                <label class="data-search" for="search-input">
                    <x-admin-icon name="search" size="15"/>
                    <input type="search" id="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Cari video...">
                </label>
                <button type="submit" class="data-filter-submit">
                    <x-admin-icon name="search" size="15"/>
                    <span>Cari</span>
                </button>
                @if(($search ?? '') !== '' || ($kategori ?? '') !== '' || ($perPage ?? 10) != 10)
                    <a href="{{ route('admin.galeri-video.index') }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="admin-card-body flush">
            @if($videos->isNotEmpty())
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Thumbnail</th>
                                <th>Judul & Kategori</th>
                                <th>URL YouTube</th>
                                <th>Tanggal Kegiatan</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($videos as $video)
                                <tr>
                                    <td>
                                        <div class="content-thumb" style="width: 72px; height: 48px; border-radius: 6px; overflow: hidden; position: relative;">
                                            <img src="{{ $video->thumbnail_url }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $video->judul }}">
                                            <div style="position: absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:white; background:rgba(220,38,38,0.8); width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                                <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor"><path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/></svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 2px;">{{ $video->judul }}</strong>
                                        <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;">{{ $video->kategori }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ $video->youtube_url }}" target="_blank" class="text-primary text-decoration-none small">
                                            <x-admin-icon name="external" size="13" class="me-1"/>Buka Video
                                        </a>
                                    </td>
                                    <td>
                                        {{ $video->tanggal ? $video->tanggal->translatedFormat('d F Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.galeri-video.edit', $video) }}" class="action-button" title="Edit video">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.galeri-video.destroy', $video) }}" method="POST" onsubmit="return confirm('Hapus video ini? Tindakan ini tidak dapat dibatalkan.')">
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

                @if($videos->hasPages())
                    <footer class="admin-card-footer">
                        <span>Halaman {{ $videos->currentPage() }} dari {{ $videos->lastPage() }}</span>
                        <div class="pager">
                            @if($videos->onFirstPage())
                                <span class="pager-link disabled">Sebelumnya</span>
                            @else
                                <a href="{{ $videos->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                            @endif

                            @for ($i = 1; $i <= $videos->lastPage(); $i++)
                                @if ($i == $videos->currentPage())
                                    <span class="pager-link active">{{ $i }}</span>
                                @else
                                    <a href="{{ $videos->url($i) }}" class="pager-link">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($videos->hasMorePages())
                                <a href="{{ $videos->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                            @else
                                <span class="pager-link disabled">Berikutnya</span>
                            @endif
                        </div>
                    </footer>
                @endif
            @else
                <div class="empty-state py-5">
                    <x-admin-icon name="youtube" size="48" class="text-muted mb-2"/>
                    <p class="mb-0">Belum ada data video galeri.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
