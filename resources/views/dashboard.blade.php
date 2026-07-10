@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_kicker', 'Ringkasan hari ini')
@section('page_title', 'Dashboard')
@section('page_description', 'Pantau data sekolah dan kelola konten website dari satu tempat.')


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
                    <div class="stat-label">Total Alumni</div>
                    <div class="stat-value">{{ number_format($countAlumni) }}</div>
                </div>
                <span class="stat-icon purple" style="color: #7c3aed; background: #f5f3ff;"><x-admin-icon name="graduation" size="21"/></span>
            </div>
            <a href="{{ route('admin.alumni.index') }}" class="stat-link">
                Lihat data <x-admin-icon name="arrow-right" size="14"/>
            </a>
        </article>

        <article class="stat-card">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Guru & Staf</div>
                    <div class="stat-value">{{ number_format($countGuru) }}</div>
                </div>
                <span class="stat-icon green"><x-admin-icon name="guru_staff" size="21"/></span>
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

            <section class="admin-card dashboard-activity-summary">
                <header class="admin-card-header">
                    <div>
                        <h2 class="admin-card-title">Aktivitas Terakhir</h2>
                        <p class="admin-card-subtitle">Riwayat perubahan dan autentikasi terbaru.</p>
                    </div>
                    <a href="{{ route('admin.activities.index') }}" class="admin-card-link">Lihat semua</a>
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
                                    <strong>{{ $activity->module }} - {{ $activity->action_type }}</strong>
                                    <p>{{ Str::limit($activity->description, 90) }}</p>
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

            <section class="admin-card dashboard-attention-card">
                <header class="admin-card-header">
                    <div>
                        <h2 class="admin-card-title">Perlu Ditindaklanjuti</h2>
                        <p class="admin-card-subtitle">Data yang membutuhkan perhatian admin.</p>
                    </div>
                </header>
                <div class="admin-card-body flush">
                    <div class="attention-list">
                        <a href="{{ route('admin.siswa.index', ['status' => 'aktif']) }}" class="attention-item">
                            <span class="attention-icon yellow"><x-admin-icon name="students" size="19"/></span>
                            <span class="attention-copy">
                                <strong>Siswa tanpa kelas</strong>
                                <small>Lengkapi penempatan kelas siswa aktif.</small>
                            </span>
                            <span class="attention-count {{ $countSiswaTanpaKelas > 0 ? 'warning' : 'clear' }}">
                                {{ number_format($countSiswaTanpaKelas) }}
                            </span>
                        </a>
                        <a href="{{ route('admin.guru-staff.index') }}" class="attention-item">
                            <span class="attention-icon blue"><x-admin-icon name="guru_staff" size="19"/></span>
                            <span class="attention-copy">
                                <strong>Data guru/staf belum lengkap</strong>
                                <small>Periksa foto dan biodata utama.</small>
                            </span>
                            <span class="attention-count {{ $countGuruBelumLengkap > 0 ? 'warning' : 'clear' }}">
                                {{ number_format($countGuruBelumLengkap) }}
                            </span>
                        </a>
                        <a href="{{ route('admin.pesan.index') }}" class="attention-item">
                            <span class="attention-icon red"><x-admin-icon name="message" size="19"/></span>
                            <span class="attention-copy">
                                <strong>Pesan belum dibaca</strong>
                                <small>Tinjau pesan terbaru dari pengunjung.</small>
                            </span>
                            <span class="attention-count {{ $countPesanBelumDibaca > 0 ? 'danger' : 'clear' }}">
                                {{ number_format($countPesanBelumDibaca) }}
                            </span>
                        </a>
                        <a href="{{ route('admin.berita.index') }}" class="attention-item">
                            <span class="attention-icon orange" style="color: #ea580c; background: #fff7ed;"><x-admin-icon name="news" size="19"/></span>
                            <span class="attention-copy">
                                <strong>Artikel berita draf</strong>
                                <small>Lengkapi dan terbitkan artikel draf.</small>
                            </span>
                            <span class="attention-count {{ $countBeritaDraft > 0 ? 'warning' : 'clear' }}">
                                {{ number_format($countBeritaDraft) }}
                            </span>
                        </a>
                        <a href="{{ route('admin.ekstrakurikuler.index') }}" class="attention-item">
                            <span class="attention-icon purple" style="color: #7c3aed; background: #f5f3ff;"><x-admin-icon name="ekstrakurikuler" size="19"/></span>
                            <span class="attention-copy">
                                <strong>Ekskul belum lengkap</strong>
                                <small>Periksa pembina dan jadwal ekskul.</small>
                            </span>
                            <span class="attention-count {{ $countEkskulBelumLengkap > 0 ? 'warning' : 'clear' }}">
                                {{ number_format($countEkskulBelumLengkap) }}
                            </span>
                        </a>
                        <a href="{{ route('admin.prestasi.index') }}" class="attention-item">
                            <span class="attention-icon green" style="color: #16a34a; background: #f0fdf4;"><x-admin-icon name="award" size="19"/></span>
                            <span class="attention-copy">
                                <strong>Prestasi tanpa foto</strong>
                                <small>Unggah foto dokumentasi prestasi.</small>
                            </span>
                            <span class="attention-count {{ $countPrestasiTanpaFoto > 0 ? 'warning' : 'clear' }}">
                                {{ number_format($countPrestasiTanpaFoto) }}
                            </span>
                        </a>
                    </div>
                </div>
            </section>

            <section class="admin-card dashboard-sidebar-activity">
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

    <section class="admin-card dashboard-message-card">
        <header class="admin-card-header">
            <div>
                <h2 class="admin-card-title">Pesan Terbaru</h2>
                <p class="admin-card-subtitle">Pesan dan masukan terbaru dari pengunjung website.</p>
            </div>
            <a href="{{ route('admin.pesan.index') }}" class="admin-card-link">Buka kotak masuk</a>
        </header>
        <div class="admin-card-body flush">
            <ul class="activity-list dashboard-message-list">
                @forelse($latestPesan as $pesan)
                    <li class="activity-item">
                        <span class="activity-dot"></span>
                        <div class="activity-copy">
                            <strong>{{ $pesan->nama }}</strong>
                            <p>{{ Str::limit($pesan->isi, 120) }}</p>
                            <span class="activity-time">
                                <x-admin-icon name="clock" size="12"/>
                                {{ $pesan->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="empty-state">Belum ada pesan masuk.</li>
                @endforelse
            </ul>
        </div>
    </section>
@endsection
