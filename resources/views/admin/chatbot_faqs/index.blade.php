{{--
    Halaman Daftar FAQ Chatbot (admin/chatbot_faqs/index.blade.php)
    Menampilkan daftar FAQ chatbot beserta statistik penggunaan, filter pencarian,
    dan aksi CRUD termasuk toggle status aktif/nonaktif.
--}}
@extends('layouts.admin')

@section('title', 'FAQ Chatbot')
@section('page_kicker', 'Sistem & Pesan')
@section('page_title', 'FAQ Chatbot')
@section('page_description', 'Kelola daftar pertanyaan dan jawaban yang digunakan asisten chatbot publik.')

@section('page_actions')
    <a href="{{ route('admin.chatbot-faqs.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah FAQ
    </a>
@endsection

@section('content')
    <x-admin-usage-guide
        description="Petunjuk singkat pengelolaan FAQ chatbot sekolah."
        :items="[
            'Tambah FAQ untuk menjawab pertanyaan umum pengunjung secara otomatis.',
            'Pastikan pertanyaan dan jawaban jelas dan akurat sebelum diaktifkan.',
            'Gunakan kata kunci (keywords) agar chatbot lebih mudah mencocokkan pertanyaan.',
            'FAQ nonaktif tidak akan digunakan oleh chatbot, meski tersimpan di database.',
        ]"
    />

    {{-- Statistik Ringkas --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="admin-card" style="padding: 20px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--admin-primary, #1a56db);">{{ $totalFaq }}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 4px;">Total FAQ</div>
        </div>
        <div class="admin-card" style="padding: 20px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #10b981;">{{ $activeFaq }}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 4px;">FAQ Aktif</div>
        </div>
        <div class="admin-card" style="padding: 20px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #f59e0b;">{{ $inactiveFaq }}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 4px;">FAQ Nonaktif</div>
        </div>
        <div class="admin-card" style="padding: 20px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #8b5cf6;">{{ $totalLogs }}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 4px;">Total Pertanyaan</div>
        </div>
        <div class="admin-card" style="padding: 20px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #10b981;">{{ $faqLogs }}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 4px;">Jawaban FAQ</div>
        </div>
        <div class="admin-card" style="padding: 20px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: #6b7280;">{{ $fallbackLogs }}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 4px;">Jawaban Fallback</div>
        </div>
    </div>

    {{-- Daftar FAQ --}}
    <section class="admin-card">
        <header class="admin-card-header admin-card-header-with-search">
            <div>
                <h2 class="admin-card-title">Daftar FAQ</h2>
                <div class="admin-card-subtitle">{{ $faqs->total() }} FAQ tersimpan</div>
            </div>
            <form method="GET" action="{{ route('admin.chatbot-faqs.index') }}" class="admin-card-search" aria-label="Filter FAQ">
                <label class="data-search" for="search-faq-input">
                    <x-admin-icon name="search" size="15"/>
                    <input type="search" id="search-faq-input" name="search"
                           value="{{ $search }}" placeholder="Cari pertanyaan, jawaban, atau kata kunci...">
                </label>
                <select name="category" class="form-control-admin"
                        style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control-admin"
                        style="width: auto; min-height: 38px; padding: 6px 12px; font-size: 12px; border: 1px solid #cfd8e3; border-radius: 8px; outline: none;">
                    <option value="">Semua Status</option>
                    <option value="1" @selected($status === '1')>Aktif</option>
                    <option value="0" @selected($status === '0')>Nonaktif</option>
                </select>
                <button type="submit" class="data-filter-submit">
                    <x-admin-icon name="search" size="15"/>
                    <span>Filter</span>
                </button>
                @if($search !== '' || $category !== '' || $status !== '')
                    <a href="{{ route('admin.chatbot-faqs.index') }}" class="data-reset">Reset</a>
                @endif
            </form>
        </header>

        <div class="admin-card-body flush">
            @if($faqs->isNotEmpty())
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 48px; text-align: center;">No</th>
                                <th>Pertanyaan</th>
                                <th style="width: 100px;">Kategori</th>
                                <th style="width: 180px;">Kata Kunci</th>
                                <th class="text-center" style="width: 80px;">Status</th>
                                <th class="text-center" style="width: 70px;">Digunakan</th>
                                <th class="text-center" style="width: 110px;">Dibuat</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $item)
                                <tr>
                                    <td class="text-center">{{ $faqs->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div style="font-weight: 500;">{{ Str::limit($item->question, 80) }}</div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted, #6b7280); margin-top: 2px;">
                                            {{ Str::limit($item->answer, 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->category)
                                            <span class="status-badge status-muted">{{ $item->category }}</span>
                                        @else
                                            <span style="color: #9ca3af; font-size: 0.8rem;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 0.78rem; color: var(--text-muted, #6b7280);">
                                            {{ $item->keywords ? Str::limit($item->keywords, 60) : '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $item->is_active ? 'status-success' : 'status-muted' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $item->usage_count }}</td>
                                    <td class="text-center" style="font-size: 0.8rem;">
                                        {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.chatbot-faqs.edit', $item) }}" class="action-button" title="Edit FAQ">Edit</a>

                                            {{-- Toggle Status --}}
                                            <form action="{{ route('admin.chatbot-faqs.toggle-status', $item) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="action-button {{ $item->is_active ? 'action-warning' : '' }}"
                                                        title="{{ $item->is_active ? 'Nonaktifkan FAQ' : 'Aktifkan FAQ' }}"
                                                        onclick="return confirm('{{ $item->is_active ? 'Nonaktifkan FAQ ini?' : 'Aktifkan FAQ ini?' }}')">
                                                    {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            {{-- Hapus --}}
                                            <form action="{{ route('admin.chatbot-faqs.destroy', $item) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-button action-danger"
                                                        onclick="return confirm('Hapus FAQ ini? Data akan diarsipkan dan tidak lagi digunakan oleh chatbot.')">
                                                    Hapus
                                                </button>
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
                    @if($search !== '' || $category !== '' || $status !== '')
                        <strong>FAQ tidak ditemukan</strong>
                        <p>Tidak ada FAQ yang cocok dengan filter yang diterapkan.</p>
                        <a href="{{ route('admin.chatbot-faqs.index') }}" class="btn-admin">Tampilkan Semua</a>
                    @else
                        <strong>Belum ada FAQ</strong>
                        <p>Mulai dengan menambahkan FAQ pertama untuk chatbot sekolah.</p>
                        <a href="{{ route('admin.chatbot-faqs.create') }}" class="btn-admin">Tambah FAQ</a>
                    @endif
                </div>
            @endif
        </div>

        @if($faqs->hasPages())
            <footer class="admin-card-footer">
                <span>Halaman {{ $faqs->currentPage() }} dari {{ $faqs->lastPage() }}</span>
                <div class="pager">
                    @if($faqs->onFirstPage())
                        <span class="pager-link disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $faqs->previousPageUrl() }}" class="pager-link">Sebelumnya</a>
                    @endif

                    @for ($i = 1; $i <= $faqs->lastPage(); $i++)
                        @if ($i == $faqs->currentPage())
                            <span class="pager-link active">{{ $i }}</span>
                        @else
                            <a href="{{ $faqs->url($i) }}" class="pager-link">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($faqs->hasMorePages())
                        <a href="{{ $faqs->nextPageUrl() }}" class="pager-link">Berikutnya</a>
                    @else
                        <span class="pager-link disabled">Berikutnya</span>
                    @endif
                </div>
            </footer>
        @endif
    </section>

    {{-- FAQ dengan Penggunaan Tertinggi --}}
    @if($topFaqs->isNotEmpty())
        <section class="admin-card" style="margin-top: 24px;">
            <header class="admin-card-header">
                <div>
                    <h2 class="admin-card-title">FAQ Paling Sering Digunakan</h2>
                    <div class="admin-card-subtitle">5 FAQ dengan jumlah penggunaan tertinggi</div>
                </div>
            </header>
            <div class="admin-card-body flush">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pertanyaan</th>
                                <th style="width: 100px;">Kategori</th>
                                <th class="text-center" style="width: 100px;">Digunakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topFaqs as $top)
                                <tr>
                                    <td>{{ Str::limit($top->question, 90) }}</td>
                                    <td>{{ $top->category ?? '—' }}</td>
                                    <td class="text-center"><strong>{{ $top->usage_count }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    {{-- Pertanyaan Chatbot Terbaru --}}
    @if($recentLogs->isNotEmpty())
        <section class="admin-card" style="margin-top: 24px;">
            <header class="admin-card-header">
                <div>
                    <h2 class="admin-card-title">Pertanyaan Chatbot Terbaru</h2>
                    <div class="admin-card-subtitle">10 pertanyaan terakhir dari pengunjung</div>
                </div>
            </header>
            <div class="admin-card-body flush">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pertanyaan</th>
                                <th class="text-center" style="width: 100px;">Sumber Jawaban</th>
                                <th class="text-center" style="width: 80px;">Status</th>
                                <th class="text-center" style="width: 140px;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogs as $log)
                                <tr>
                                    <td>{{ Str::limit($log->question, 100) }}</td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $log->answer_source === 'faq' ? 'status-success' : 'status-muted' }}">
                                            {{ $log->answer_source === 'faq' ? 'FAQ' : 'Fallback' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $log->status === 'success' ? 'status-success' : 'status-muted' }}">
                                            {{ $log->status === 'success' ? 'OK' : ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="font-size: 0.8rem;">
                                        {{ $log->created_at ? $log->created_at->translatedFormat('d M Y H:i') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
@endsection
