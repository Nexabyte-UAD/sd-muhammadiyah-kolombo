@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_kicker', 'Ringkasan hari ini')
@section('page_title', 'Dashboard')
@section('page_description', 'Pantau data sekolah dan kelola konten website dari satu tempat.')

@section('page_actions')
    <a href="{{ route('admin.berita.create') }}" class="btn-admin">
        <x-admin-icon name="plus" size="18"/>
        Tambah Berita
    </a>
@endsection

@section('content')
    <section class="stats-grid" aria-label="Statistik sekolah">
        <article class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Siswa Aktif</div>
                    <div class="stat-value">{{ number_format($countSiswa) }}</div>
                </div>
                <span class="stat-icon"><x-admin-icon name="students" size="21"/></span>
            </div>
            <a href="{{ route('admin.siswa.index', ['status' => 'aktif']) }}" class="stat-link">
                Kelola data <x-admin-icon name="arrow-right" size="14"/>
            </a>
        </article>

        <article class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Guru &amp; Staf</div>
                    <div class="stat-value">{{ number_format($countGuru) }}</div>
                </div>
                <span class="stat-icon green"><x-admin-icon name="users" size="21"/></span>
            </div>
            <a href="{{ route('admin.guru-staff.index') }}" class="stat-link">
                Kelola data <x-admin-icon name="arrow-right" size="14"/>
            </a>
        </article>

        <article class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Berita Terbit</div>
                    <div class="stat-value">{{ number_format($countBerita) }}</div>
                </div>
                <span class="stat-icon yellow"><x-admin-icon name="news" size="21"/></span>
            </div>
            <a href="{{ route('admin.berita.index') }}" class="stat-link">
                Lihat berita <x-admin-icon name="arrow-right" size="14"/>
            </a>
        </article>

        <article class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Pesan Masuk</div>
                    <div class="stat-value">{{ number_format($countPesan) }}</div>
                </div>
                <span class="stat-icon red"><x-admin-icon name="message" size="21"/></span>
            </div>
            <a href="{{ route('admin.pesan.index') }}" class="stat-link">
                Buka pesan <x-admin-icon name="arrow-right" size="14"/>
            </a>
        </article>
    </section>

    <div class="dashboard-grid">
        <div>
            <section class="admin-card">
                <header class="admin-card-header">
                    <h2 class="admin-card-title">Berita Terbaru</h2>
                    <a href="{{ route('admin.berita.index') }}" class="admin-card-link">Lihat semua</a>
                </header>
                <div class="admin-card-body flush">
                    @if($latestBerita->isNotEmpty())
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Berita</th>
                                        <th>Tanggal Terbit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestBerita as $berita)
                                        <tr>
                                            <td>
                                                <div class="content-cell">
                                                    <div class="content-thumb">
                                                        @if($berita->gambar)
                                                            <img src="{{ asset('storage/' . $berita->gambar) }}" alt="">
                                                        @else
                                                            <x-admin-icon name="news" size="19"/>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="content-title">{{ Str::limit($berita->judul, 58) }}</div>
                                                        <div class="content-meta">Diperbarui {{ $berita->updated_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ optional($berita->tanggal)->translatedFormat('d M Y') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">Belum ada berita yang diterbitkan.</div>
                    @endif
                </div>
            </section>

            <section class="admin-card">
                <header class="admin-card-header">
                    <h2 class="admin-card-title">Pesan Terbaru</h2>
                    <a href="{{ route('admin.pesan.index') }}" class="admin-card-link">Buka kotak masuk</a>
                </header>
                <div class="admin-card-body flush">
                    @forelse($latestPesan as $pesan)
                        <div class="activity-item">
                            <span class="activity-dot"></span>
                            <div class="activity-copy">
                                <strong>{{ $pesan->nama }}</strong>
                                <p>{{ Str::limit($pesan->isi, 90) }}</p>
                                <span class="activity-time">
                                    <x-admin-icon name="clock" size="12"/>
                                    {{ $pesan->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">Belum ada pesan masuk.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside>
            <section class="admin-card">
                <header class="admin-card-header">
                    <h2 class="admin-card-title">Akses Cepat</h2>
                </header>
                <div class="admin-card-body">
                    <div class="quick-actions">
                        <a href="{{ route('admin.berita.create') }}" class="quick-action">
                            <x-admin-icon name="news" size="21"/>
                            Tambah Berita
                        </a>
                        <a href="{{ route('admin.siswa.create') }}" class="quick-action">
                            <x-admin-icon name="students" size="21"/>
                            Tambah Siswa
                        </a>
                        <a href="{{ route('admin.prestasi.create') }}" class="quick-action">
                            <x-admin-icon name="award" size="21"/>
                            Tambah Prestasi
                        </a>
                        <a href="{{ route('admin.settings.edit') }}" class="quick-action">
                            <x-admin-icon name="settings" size="21"/>
                            Pengaturan
                        </a>
                    </div>
                </div>
            </section>

            <section class="admin-card">
                <header class="admin-card-header">
                    <h2 class="admin-card-title">Aktivitas Terakhir</h2>
                </header>
                <div class="admin-card-body flush">
                    <ul class="activity-list">
                        @forelse($recentActivities as $activity)
                            @php
                                $dotClass = match($activity->action_type) {
                                    'Tambah' => 'green',
                                    'Update' => 'yellow',
                                    'Hapus' => 'red',
                                    default => '',
                                };
                            @endphp
                            <li class="activity-item">
                                <span class="activity-dot {{ $dotClass }}"></span>
                                <div class="activity-copy">
                                    <strong>{{ $activity->module }} · {{ $activity->action_type }}</strong>
                                    <p>{{ Str::limit($activity->description, 68) }}</p>
                                    <span class="activity-time">
                                        <x-admin-icon name="clock" size="12"/>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="empty-state">Belum ada aktivitas tercatat.</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </aside>
    </div>
@endsection
