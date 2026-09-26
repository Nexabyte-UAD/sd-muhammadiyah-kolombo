@extends('layouts.admin')

@section('title', 'Guru Menulis')
@section('page_kicker', 'Manajemen Konten')
@section('page_title', 'Guru Menulis (Artikel & Karya Tulis)')
@section('page_description', 'Kelola publikasi artikel, opini, dan karya tulis para guru SD Muhammadiyah Kolombo.')

@section('page_actions')
    <a href="{{ route('admin.guru-menulis.create') }}" class="btn-admin primary">
        <x-admin-icon name="plus" size="18"/>
        <span>Tambah Artikel Baru</span>
    </a>
@endsection

@section('content')
    <x-admin-usage-guide
        description="Petunjuk singkat pengelolaan karya tulis & artikel Guru Menulis."
        :items="[
            'Klik Tambah Artikel Baru untuk mempublikasikan karya tulis, artikel, atau opini bapak/ibu guru.',
            'Nama penulis dapat dipilih langsung dari database guru/staf atau diketik secara manual.',
            'Pilih atau buat kategori artikel agar tulisan mudah dicari pada halaman publik.',
            'Gunakan status Published untuk langsung menerbitkan ke web, atau Draft untuk menyimpan draf artikel.'
        ]"
    />

<section class="admin-card">
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar Artikel Guru Menulis</h2>
                <div class="admin-card-subtitle">{{ $artikels->total() }} artikel tersimpan</div>
            </div>
            
            <form method="GET" action="{{ route('admin.guru-menulis.index') }}" class="admin-card-search" aria-label="Cari artikel">
                <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
                </select>

                <select name="kategori" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" @selected($kategori === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>

                <label class="data-search" for="search-input">
                    <x-admin-icon name="search" size="15"/>
                    <input type="search" id="search-input" name="search" value="{{ $search }}" placeholder="Cari artikel / penulis...">
                </label>
                <button type="submit" class="data-filter-submit">
                    <x-admin-icon name="search" size="15"/>
                    <span>Cari</span>
                </button>
                @if($search !== '' || $kategori !== '' || $perPage != 10)
                    <a href="{{ route('admin.guru-menulis.index') }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="admin-card-body flush">
            @if($artikels->isNotEmpty())
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Artikel & Penulis</th>
                                <th>Kategori</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($artikels as $item)
                                <tr>
                                    <td>
                                        <div class="content-cell">
                                            <div class="content-thumb" style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden;">
                                                @if($item->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->gambar))
                                                    <img src="{{ asset('storage/' . $item->gambar) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="">
                                                @else
                                                    <x-admin-icon name="pencil" size="22" class="text-muted"/>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="content-title">
                                                    <a href="{{ route('guru-menulis.detail', $item->slug) }}" target="_blank" style="text-decoration: none; color: inherit;" title="Pratinjau Artikel">
                                                        {{ $item->judul }} <x-admin-icon name="external" size="12" class="text-muted" style="margin-left: 4px;"/>
                                                    </a>
                                                </div>
                                                <div class="content-meta">Penulis: {{ $item->penulis }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-uppercase" style="font-size: 0.65rem;">{{ $item->kategori }}</span>
                                    </td>
                                    <td class="text-center">{{ optional($item->tanggal)->translatedFormat('d M Y') ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $item->status === 'published' ? 'status-success' : 'status-muted' }}">
                                            {{ $item->status === 'published' ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.guru-menulis.edit', $item) }}" class="action-button" title="Edit artikel">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.guru-menulis.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus artikel ini? Tindakan ini tidak dapat dibatalkan.')">
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

    @if($artikels->hasPages())
        <footer class="admin-card-footer">
            <span>Halaman {{ $artikels->currentPage() }} dari {{ $artikels->lastPage() }}</span>
            <div class="pager">
                @if($artikels->onFirstPage())
                    <span class="pager-link disabled">Sebelumnya</span>
                @else
                    <a href="{{ $artikels->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                @endif

                @for ($i = 1; $i <= $artikels->lastPage(); $i++)
                    @if ($i == $artikels->currentPage())
                        <span class="pager-link active">{{ $i }}</span>
                    @else
                        <a href="{{ $artikels->url($i) }}" class="pager-link">{{ $i }}</a>
                    @endif
                @endfor

                @if($artikels->hasMorePages())
                    <a href="{{ $artikels->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                @else
                    <span class="pager-link disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    @endif
            @else
                <div class="empty-state py-5">
                    <x-admin-icon name="pencil" size="48" class="text-muted mb-2"/>
                    <p class="mb-0">Belum ada data artikel Guru Menulis.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
