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
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar Berita</h2>
                <div class="admin-card-subtitle">{{ $beritas->total() }} berita tersimpan</div>
            </div>
            <form method="GET" action="{{ route('admin.berita.index') }}" class="admin-card-search" aria-label="Cari berita">
                <select name="per_page" class="form-control-admin" style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;" onchange="this.form.submit()">
                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 baris</option>
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
                <label class="data-search" for="search-input">
                    <i class="fas fa-search"></i>
                    <input type="search" id="search-input" name="search" value="{{ $search }}" placeholder="Cari judul atau isi berita...">
                </label>
                <button type="submit" class="data-filter-submit">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                @if($search !== '')
                    <a href="{{ route('admin.berita.index') }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="admin-card-body flush">
            @if($beritas->isNotEmpty())
                <div class="admin-table-wrap">
                    <table class="admin-table berita-admin-table">
                        <thead>
                            <tr>
                                <th>Berita</th>
                                <th class="text-center">Tanggal Rilis</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
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
                                                <div class="content-title">
                                                    <a href="{{ route('berita.detail', $item) }}" target="_blank" style="text-decoration: none; color: inherit;" title="Pratinjau Berita">
                                                        {{ $item->judul }} <i class="fas fa-external-link-alt text-muted" style="font-size: 10px; margin-left: 4px;"></i>
                                                    </a>
                                                </div>
                                                <div class="content-meta">{{ Str::limit(strip_tags($item->isi), 78) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $item->status === 'published' ? 'status-success' : 'status-muted' }}">
                                            {{ $item->status === 'published' ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
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
                    @if($search !== '')
                        <strong>Berita tidak ditemukan</strong>
                        <p>Tidak ada berita yang cocok dengan pencarian "{{ $search }}".</p>
                        <a href="{{ route('admin.berita.index') }}" class="btn-admin">Tampilkan Semua</a>
                    @else
                        <strong>Belum ada berita</strong>
                        <p>Mulai dengan menambahkan publikasi pertama sekolah.</p>
                        <a href="{{ route('admin.berita.create') }}" class="btn-admin">Tulis Berita</a>
                    @endif
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

                    @for ($i = 1; $i <= $beritas->lastPage(); $i++)
                        @if ($i == $beritas->currentPage())
                            <span class="pager-link active">{{ $i }}</span>
                        @else
                            <a href="{{ $beritas->url($i) }}" class="pager-link">{{ $i }}</a>
                        @endif
                    @endfor

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
