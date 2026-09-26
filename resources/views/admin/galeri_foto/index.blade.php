@extends('layouts.admin')

@section('title', 'Galeri Foto')
@section('page_kicker', 'Manajemen Konten')
@section('page_title', 'Galeri Foto Sekolah')
@section('page_description', 'Kelola album dan dokumentasi foto kegiatan SD Muhammadiyah Komplek Kolombo.')

@section('page_actions')
    <a href="{{ route('admin.galeri-foto.create') }}" class="btn-admin primary">
        <x-admin-icon name="plus" size="18"/>
        <span>Tambah Foto Baru</span>
    </a>
@endsection

@section('content')
    <section class="admin-card">
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar Foto Galeri</h2>
                <div class="admin-card-subtitle">{{ $fotos->total() }} foto tersimpan</div>
            </div>
            
            <form method="GET" action="{{ route('admin.galeri-foto.index') }}" class="admin-card-search" aria-label="Cari foto">
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
                    <input type="search" id="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Cari foto...">
                </label>
                <button type="submit" class="data-filter-submit">
                    <x-admin-icon name="search" size="15"/>
                    <span>Cari</span>
                </button>
                @if(($search ?? '') !== '' || ($kategori ?? '') !== '' || ($perPage ?? 10) != 10)
                    <a href="{{ route('admin.galeri-foto.index') }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="admin-card-body flush">
            @if($fotos->isNotEmpty())
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Foto</th>
                                <th>Judul & Kategori</th>
                                <th>Tanggal Kegiatan</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fotos as $foto)
                                <tr>
                                    <td>
                                        <div class="content-thumb" style="width: 54px; height: 54px; border-radius: 8px; overflow: hidden;">
                                            @if($foto->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($foto->foto))
                                                <img src="{{ asset('storage/' . $foto->foto) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $foto->judul }}">
                                            @else
                                                <x-admin-icon name="image" size="24" class="text-muted"/>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 2px;">{{ $foto->judul }}</strong>
                                        <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;">{{ $foto->kategori }}</span>
                                    </td>
                                    <td>
                                        {{ $foto->tanggal ? $foto->tanggal->translatedFormat('d F Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.galeri-foto.edit', $foto) }}" class="action-button" title="Edit foto">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.galeri-foto.destroy', $foto) }}" method="POST" onsubmit="return confirm('Hapus foto ini? Tindakan ini tidak dapat dibatalkan.')">
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

                @if($fotos->hasPages())
                    <footer class="admin-card-footer">
                        <span>Halaman {{ $fotos->currentPage() }} dari {{ $fotos->lastPage() }}</span>
                        <div class="pager">
                            @if($fotos->onFirstPage())
                                <span class="pager-link disabled">Sebelumnya</span>
                            @else
                                <a href="{{ $fotos->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                            @endif

                            @for ($i = 1; $i <= $fotos->lastPage(); $i++)
                                @if ($i == $fotos->currentPage())
                                    <span class="pager-link active">{{ $i }}</span>
                                @else
                                    <a href="{{ $fotos->url($i) }}" class="pager-link">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($fotos->hasMorePages())
                                <a href="{{ $fotos->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                            @else
                                <span class="pager-link disabled">Berikutnya</span>
                            @endif
                        </div>
                    </footer>
                @endif
            @else
                <div class="empty-state py-5">
                    <x-admin-icon name="camera" size="48" class="text-muted mb-2"/>
                    <p class="mb-0">Belum ada data foto galeri.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
