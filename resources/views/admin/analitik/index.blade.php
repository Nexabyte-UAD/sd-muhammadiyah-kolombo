{{--
    Halaman Analitik Admin (admin/analitik/index.blade.php)
    Menampilkan dashboard analitik lengkap dengan:
    - Statistik pengunjung website (custom page view tracking)
    - Tren kunjungan harian (line chart 30 hari)
    - Top halaman paling banyak dikunjungi
    - Chart data internal: siswa, prestasi, berita, pesan, guru
    Library chart: Chart.js (CDN)
--}}
@extends('layouts.admin')

@section('title', 'Analitik')
@section('page_kicker', 'Insight & Statistik')
@section('page_title', 'Analitik')
@section('page_description', 'Pantau trafik pengunjung website dan insight data sekolah secara visual.')

@push('styles')
<style>
/* ── Analitik Layout ─────────────────────────────────────────── */
.analitik-hero {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.75rem;
}
@media (max-width: 1100px) { .analitik-hero { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .analitik-hero { grid-template-columns: 1fr; } }

.hero-card {
    background: var(--admin-white, #fff);
    border: 1px solid var(--admin-border, #e5e7eb);
    border-radius: 14px;
    padding: 1.25rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: box-shadow .2s, transform .2s;
}
.hero-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.07); transform: translateY(-2px); }

.hero-card-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 22px;
}
.hero-card-icon.blue   { background: #eff6ff; color: #3b82f6; }
.hero-card-icon.green  { background: #f0fdf4; color: #22c55e; }
.hero-card-icon.purple { background: #f5f3ff; color: #7c3aed; }
.hero-card-icon.amber  { background: #fffbeb; color: #f59e0b; }

.hero-card-body { min-width: 0; }
.hero-card-label {
    font-size: .72rem; font-weight: 600; letter-spacing: .04em;
    color: var(--admin-text-muted, #6b7280);
    text-transform: uppercase; margin-bottom: .2rem;
}
.hero-card-value {
    font-size: 1.75rem; font-weight: 800;
    color: var(--admin-text, #111827);
    line-height: 1;
}
.hero-card-sub {
    font-size: .75rem; color: var(--admin-text-muted, #6b7280); margin-top: .25rem;
}

/* ── Chart Grid ─────────────────────────────────────────────── */
.analitik-section-title {
    font-size: .7rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: var(--admin-text-muted, #6b7280);
    margin: 2rem 0 .9rem;
    display: flex; align-items: center; gap: .5rem;
}
.analitik-section-title::after {
    content: ''; flex: 1; height: 1px;
    background: var(--admin-border, #e5e7eb);
}

.chart-grid {
    display: grid;
    gap: 1.25rem;
}
.chart-grid-2  { grid-template-columns: repeat(2, 1fr); }
.chart-grid-3  { grid-template-columns: repeat(3, 1fr); }
.chart-grid-full { grid-template-columns: 1fr; }

@media (max-width: 1100px) {
    .chart-grid-3 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .chart-grid-2, .chart-grid-3 { grid-template-columns: 1fr; }
}

.chart-card {
    background: var(--admin-white, #fff);
    border: 1px solid var(--admin-border, #e5e7eb);
    border-radius: 14px;
    padding: 1.4rem 1.5rem 1.25rem;
    display: flex; flex-direction: column;
    transition: box-shadow .2s;
}
.chart-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); }
.chart-card.span-2 { grid-column: span 2; }
@media (max-width: 768px) { .chart-card.span-2 { grid-column: span 1; } }

.chart-card-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    margin-bottom: 1.1rem;
}
.chart-card-title {
    font-size: .85rem; font-weight: 700;
    color: var(--admin-text, #111827);
}
.chart-card-subtitle {
    font-size: .72rem; color: var(--admin-text-muted, #6b7280); margin-top: .1rem;
}
.chart-card-badge {
    font-size: .68rem; font-weight: 600; padding: .2rem .6rem;
    border-radius: 999px; background: #eff6ff; color: #3b82f6;
    white-space: nowrap;
}
.chart-card-badge.green { background: #f0fdf4; color: #16a34a; }
.chart-card-badge.purple { background: #f5f3ff; color: #7c3aed; }
.chart-card-badge.amber { background: #fffbeb; color: #d97706; }

.chart-wrap { flex: 1; position: relative; min-height: 180px; }
.chart-wrap canvas { max-height: 260px; }

/* ── Top Halaman Table ──────────────────────────────────────── */
.top-pages-table { width: 100%; border-collapse: collapse; }
.top-pages-table th {
    font-size: .68rem; font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; color: var(--admin-text-muted, #6b7280);
    text-align: left; padding: .5rem .75rem; border-bottom: 1px solid var(--admin-border, #e5e7eb);
}
.top-pages-table td {
    font-size: .82rem; padding: .6rem .75rem;
    border-bottom: 1px solid var(--admin-border, #e5e7eb);
    color: var(--admin-text, #111827);
}
.top-pages-table tr:last-child td { border-bottom: none; }
.top-pages-table tr:hover td { background: var(--admin-bg, #f9fafb); }
.page-bar-wrap { display: flex; align-items: center; gap: .75rem; }
.page-bar {
    flex: 1; height: 6px; background: var(--admin-border, #e5e7eb); border-radius: 999px; overflow: hidden;
}
.page-bar-fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, #3b82f6, #6366f1);
    transition: width .4s ease;
}
.page-rank {
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--admin-bg, #f3f4f6);
    font-size: .68rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    color: var(--admin-text-muted, #6b7280);
    flex-shrink: 0;
}
.page-rank.top { background: #fef3c7; color: #d97706; }

/* ── Empty state ──────────────────────────────────────────── */
.analitik-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 2.5rem 1rem; color: var(--admin-text-muted, #9ca3af);
    gap: .5rem;
}
.analitik-empty svg { opacity: .3; }
.analitik-empty p { font-size: .82rem; margin: 0; }
</style>
@endpush

@section('content')

{{-- ── Hero: 4 Kartu Utama Pengunjung ───────────────────────────── --}}
<div class="analitik-hero">
    <div class="hero-card">
        <div class="hero-card-icon blue">
            <x-admin-icon name="eye" size="22"/>
        </div>
        <div class="hero-card-body">
            <div class="hero-card-label">Pengunjung Hari Ini</div>
            <div class="hero-card-value">{{ number_format($pengunjungHariIni) }}</div>
            <div class="hero-card-sub">halaman dilihat</div>
        </div>
    </div>
    <div class="hero-card">
        <div class="hero-card-icon green">
            <x-admin-icon name="trending-up" size="22"/>
        </div>
        <div class="hero-card-body">
            <div class="hero-card-label">30 Hari Terakhir</div>
            <div class="hero-card-value">{{ number_format($pengunjungBulanIni) }}</div>
            <div class="hero-card-sub">total kunjungan</div>
        </div>
    </div>
    <div class="hero-card">
        <div class="hero-card-icon purple">
            <x-admin-icon name="users" size="22"/>
        </div>
        <div class="hero-card-body">
            <div class="hero-card-label">Unique Visitor</div>
            <div class="hero-card-value">{{ number_format($uniqueVisitor30Hari) }}</div>
            <div class="hero-card-sub">30 hari terakhir</div>
        </div>
    </div>
    <div class="hero-card">
        <div class="hero-card-icon amber">
            <x-admin-icon name="chart" size="22"/>
        </div>
        <div class="hero-card-body">
            <div class="hero-card-label">Total Kunjungan</div>
            <div class="hero-card-value">{{ number_format($totalAllTime) }}</div>
            <div class="hero-card-sub">sepanjang waktu</div>
        </div>
    </div>
</div>

{{-- ── Trafik Website ─────────────────────────────────────────────── --}}
<div class="analitik-section-title">
    <x-admin-icon name="trending-up" size="14"/>
    Trafik Website
</div>

<div class="chart-grid chart-grid-full" style="margin-bottom:1.25rem">
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Tren Kunjungan Harian</div>
                <div class="chart-card-subtitle">Total halaman dilihat per hari dalam 30 hari terakhir</div>
            </div>
            <span class="chart-card-badge">30 Hari</span>
        </div>
        @if($pengunjungBulanIni > 0)
            <div class="chart-wrap">
                <canvas id="chartTrendHarian"></canvas>
            </div>
        @else
            <div class="analitik-empty">
                <x-admin-icon name="chart" size="36"/>
                <p>Belum ada data kunjungan. Mulai terkumpul otomatis saat pengunjung membuka website.</p>
            </div>
        @endif
    </div>
</div>

<div class="chart-grid chart-grid-2">
    {{-- Top Halaman --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Halaman Terpopuler</div>
                <div class="chart-card-subtitle">Berdasarkan 30 hari terakhir</div>
            </div>
            <span class="chart-card-badge">Top 8</span>
        </div>
        @if($topHalaman->count() > 0)
        @php $maxView = $topHalaman->max('total'); @endphp
        <table class="top-pages-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Halaman</th>
                    <th>Kunjungan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topHalaman as $i => $halaman)
                <tr>
                    <td><div class="page-rank {{ $i < 3 ? 'top' : '' }}">{{ $i + 1 }}</div></td>
                    <td>
                        <div>{{ $halaman->page_label }}</div>
                        <div class="page-bar-wrap" style="margin-top:.3rem">
                            <div class="page-bar">
                                <div class="page-bar-fill" style="width:{{ $maxView > 0 ? round(($halaman->total / $maxView) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight:700; color: var(--admin-primary,#2563eb)">
                        {{ number_format($halaman->total) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div class="analitik-empty">
                <x-admin-icon name="eye" size="32"/>
                <p>Belum ada data kunjungan halaman.</p>
            </div>
        @endif
    </div>

    {{-- Pesan Masuk per Bulan --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Tren Pesan Masuk</div>
                <div class="chart-card-subtitle">Jumlah pesan dari pengunjung per bulan</div>
            </div>
            <span class="chart-card-badge">12 Bulan</span>
        </div>
        <div class="chart-wrap">
            <canvas id="chartPesanBulanan"></canvas>
        </div>
    </div>
</div>

{{-- ── Data Siswa ─────────────────────────────────────────────────── --}}
<div class="analitik-section-title" style="margin-top:2rem">
    <x-admin-icon name="students" size="14"/>
    Data Siswa
</div>

<div class="chart-grid chart-grid-3">
    {{-- Siswa per Kelas --}}
    <div class="chart-card span-2">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Siswa Aktif per Kelas</div>
                <div class="chart-card-subtitle">Distribusi jumlah siswa aktif di setiap kelas</div>
            </div>
            <span class="chart-card-badge green">Aktif</span>
        </div>
        @if($siswaPerKelas->count() > 0)
        <div class="chart-wrap">
            <canvas id="chartSiswaKelas"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="students" size="32"/>
            <p>Belum ada data siswa aktif.</p>
        </div>
        @endif
    </div>

    {{-- Jenis Kelamin --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Jenis Kelamin</div>
                <div class="chart-card-subtitle">Komposisi siswa aktif</div>
            </div>
        </div>
        @if($siswaGender->count() > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartSiswaGender" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="users" size="32"/>
            <p>Belum ada data.</p>
        </div>
        @endif
    </div>
</div>

<div class="chart-grid chart-grid-3" style="margin-top:1.25rem">
    {{-- Status Siswa --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Status Siswa</div>
                <div class="chart-card-subtitle">Aktif, Alumni, Keluar</div>
            </div>
        </div>
        @if($siswaStatus->count() > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartSiswaStatus" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="activity" size="32"/>
            <p>Belum ada data.</p>
        </div>
        @endif
    </div>

    {{-- Tren Penerimaan per Tahun --}}
    <div class="chart-card span-2">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Tren Penerimaan Siswa</div>
                <div class="chart-card-subtitle">Jumlah siswa yang masuk per tahun ajaran</div>
            </div>
            <span class="chart-card-badge purple">Per Tahun</span>
        </div>
        @if($siswaTahunan->count() > 0)
        <div class="chart-wrap">
            <canvas id="chartSiswaTahunan"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="trending-up" size="32"/>
            <p>Belum ada data tahun masuk siswa.</p>
        </div>
        @endif
    </div>
</div>

{{-- ── Prestasi & Berita ──────────────────────────────────────────── --}}
<div class="analitik-section-title" style="margin-top:2rem">
    <x-admin-icon name="award" size="14"/>
    Prestasi & Konten
</div>

<div class="chart-grid chart-grid-3">
    {{-- Prestasi per Kategori --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Prestasi per Kategori</div>
                <div class="chart-card-subtitle">Akademik, Non-akademik, Keagamaan</div>
            </div>
        </div>
        @if($prestasiKategori->count() > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartPrestasiKategori" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="award" size="32"/>
            <p>Belum ada data prestasi.</p>
        </div>
        @endif
    </div>

    {{-- Berita per Status --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Status Berita</div>
                <div class="chart-card-subtitle">Terbit vs Draft</div>
            </div>
        </div>
        @if($beritaStatus->count() > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartBeritaStatus" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="news" size="32"/>
            <p>Belum ada berita.</p>
        </div>
        @endif
    </div>

    {{-- Tren Berita per Bulan --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Berita Terbit per Bulan</div>
                <div class="chart-card-subtitle">12 bulan terakhir</div>
            </div>
            <span class="chart-card-badge amber">Konten</span>
        </div>
        <div class="chart-wrap">
            <canvas id="chartBeritaBulanan"></canvas>
        </div>
    </div>
</div>

{{-- ── Pesan & Guru ───────────────────────────────────────────────── --}}
<div class="analitik-section-title" style="margin-top:2rem">
    <x-admin-icon name="message" size="14"/>
    Pesan & Guru
</div>

<div class="chart-grid chart-grid-3" style="margin-bottom:2rem">
    {{-- Pesan Dibaca vs Belum --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Status Pesan</div>
                <div class="chart-card-subtitle">Dibaca vs Belum dibaca</div>
            </div>
        </div>
        @if($pesanStatus->sum('value') > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartPesanStatus" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="message" size="32"/>
            <p>Belum ada pesan masuk.</p>
        </div>
        @endif
    </div>

    {{-- Guru per Jenis Kelamin --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Guru & Staf</div>
                <div class="chart-card-subtitle">Komposisi jenis kelamin</div>
            </div>
        </div>
        @if($guruGender->count() > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartGuruGender" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="guru_staff" size="32"/>
            <p>Belum ada data guru/staf.</p>
        </div>
        @endif
    </div>

    {{-- Siswa per Agama --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Siswa per Agama</div>
                <div class="chart-card-subtitle">Komposisi agama siswa aktif</div>
            </div>
        </div>
        @if($siswaAgama->count() > 0)
        <div class="chart-wrap" style="display:flex;align-items:center;justify-content:center">
            <canvas id="chartSiswaAgama" style="max-height:200px;max-width:200px"></canvas>
        </div>
        @else
        <div class="analitik-empty">
            <x-admin-icon name="school" size="32"/>
            <p>Belum ada data.</p>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    // ── Data dari PHP ──────────────────────────────────────────────────────
    const trendHarian = @json($trendHarian);

    const siswaKelas   = @json($siswaPerKelas);
    const siswaGender  = @json($siswaGender);
    const siswaStatus  = @json($siswaStatus);
    const siswaTahunan = @json($siswaTahunan);
    const siswaAgama   = @json($siswaAgama);

    const prestasiKat  = @json($prestasiKategori);
    const prestasiThn  = @json($prestasiTahunan);

    const beritaStatus      = @json($beritaStatus);
    const beritaBulananLabels = @json($beritaBulananLabels);
    const beritaBulananData   = @json($beritaBulananData);

    const pesanBulananLabels = @json($pesanBulananLabels);
    const pesanBulananData   = @json($pesanBulananData);
    const pesanStatus        = @json($pesanStatus);

    const guruGender = @json($guruGender);

    // ── Warna Palet Profesional ────────────────────────────────────────────
    const MULTI_PIE = ['#4f46e5', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#6366f1', '#f43f5e'];
    const BLUE_GRADIENT = (ctx) => {
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
        return gradient;
    };
    const PURPLE_GRADIENT = (ctx) => {
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(124, 58, 237, 0.4)');
        gradient.addColorStop(1, 'rgba(124, 58, 237, 0)');
        return gradient;
    };

    // ── Helper ─────────────────────────────────────────────────────────────
    const isDark = () => document.documentElement.classList.contains('dark-mode');
    const gridColor = () => isDark() ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.04)';
    const textColor = () => isDark() ? '#9ca3af' : '#6b7280';

    const baseScales = () => ({
        x: {
            grid: { color: gridColor(), drawBorder: false },
            ticks: { color: textColor(), font: { size: 11 } }
        },
        y: {
            grid: { color: gridColor(), drawBorder: false },
            ticks: { color: textColor(), font: { size: 11 }, padding: 8 },
            beginAtZero: true
        }
    });

    Chart.defaults.font.family = "'Poppins', 'Inter', sans-serif";
    Chart.defaults.color = textColor();
    Chart.defaults.plugins.legend.labels.font = { size: 12 };
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.backgroundColor = isDark() ? 'rgba(17, 24, 39, 0.9)' : 'rgba(255, 255, 255, 0.95)';
    Chart.defaults.plugins.tooltip.titleColor = isDark() ? '#f3f4f6' : '#111827';
    Chart.defaults.plugins.tooltip.bodyColor = isDark() ? '#d1d5db' : '#4b5563';
    Chart.defaults.plugins.tooltip.borderColor = gridColor();
    Chart.defaults.plugins.tooltip.borderWidth = 1;

    // ── 1. Tren Kunjungan Harian (Area Line Chart) ────────────────────────
    const ctxTrend = document.getElementById('chartTrendHarian');
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendHarian.labels,
                datasets: [{
                    label: 'Kunjungan',
                    data: trendHarian.data,
                    borderColor: '#3b82f6',
                    backgroundColor: BLUE_GRADIENT(ctxTrend.getContext('2d')),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                interaction: { intersect: false, mode: 'index' },
                scales: baseScales()
            }
        });
    }

    // ── 2. Pesan Bulanan (Rounded Bar) ────────────────────────────────────
    const ctxPesan = document.getElementById('chartPesanBulanan');
    if (ctxPesan) {
        new Chart(ctxPesan, {
            type: 'bar',
            data: {
                labels: pesanBulananLabels,
                datasets: [{
                    label: 'Pesan Masuk',
                    data: pesanBulananData,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: baseScales()
            }
        });
    }

    // ── 3. Siswa per Kelas (Bar) ──────────────────────────────────────────
    const ctxSiswaKelas = document.getElementById('chartSiswaKelas');
    if (ctxSiswaKelas && siswaKelas.length > 0) {
        let scales = baseScales();
        scales.x.grid.display = false; // Hide vertical lines
        new Chart(ctxSiswaKelas, {
            type: 'bar',
            data: {
                labels: siswaKelas.map(d => d.label),
                datasets: [{
                    label: 'Siswa',
                    data: siswaKelas.map(d => d.value),
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: scales
            }
        });
    }

    // ── 4. Jenis Kelamin Siswa (Pie) ──────────────────────────────────────
    const ctxGender = document.getElementById('chartSiswaGender');
    if (ctxGender && siswaGender.length > 0) {
        new Chart(ctxGender, {
            type: 'pie',
            data: {
                labels: siswaGender.map(d => d.label),
                datasets: [{
                    data: siswaGender.map(d => d.value),
                    backgroundColor: ['#3b82f6', '#ec4899'],
                    borderWidth: 2,
                    borderColor: isDark() ? '#1f2937' : '#ffffff',
                    hoverOffset: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // ── 5. Status Siswa (Horizontal Bar) ──────────────────────────────────
    const ctxStatus = document.getElementById('chartSiswaStatus');
    if (ctxStatus && siswaStatus.length > 0) {
        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: siswaStatus.map(d => d.label),
                datasets: [{
                    label: 'Status',
                    data: siswaStatus.map(d => d.value),
                    backgroundColor: ['#10b981','#6366f1','#f43f5e'],
                    borderRadius: 6,
                    barPercentage: 0.7
                }]
            },
            options: {
                indexAxis: 'y', // Makes it horizontal
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor() }, ticks: { color: textColor() } },
                    y: { grid: { display: false }, ticks: { color: textColor() } }
                }
            }
        });
    }

    // ── 6. Tren Penerimaan (Area Line) ────────────────────────────────────
    const ctxTahunan = document.getElementById('chartSiswaTahunan');
    if (ctxTahunan && siswaTahunan.length > 0) {
        new Chart(ctxTahunan, {
            type: 'line',
            data: {
                labels: siswaTahunan.map(d => d.label),
                datasets: [{
                    label: 'Siswa Masuk',
                    data: siswaTahunan.map(d => d.value),
                    borderColor: '#7c3aed',
                    backgroundColor: PURPLE_GRADIENT(ctxTahunan.getContext('2d')),
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderWidth: 2,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: baseScales()
            }
        });
    }

    // ── 7. Prestasi per Kategori (Polar Area) ─────────────────────────────
    const ctxPrestasi = document.getElementById('chartPrestasiKategori');
    if (ctxPrestasi && prestasiKat.length > 0) {
        new Chart(ctxPrestasi, {
            type: 'polarArea',
            data: {
                labels: prestasiKat.map(d => d.label),
                datasets: [{
                    data: prestasiKat.map(d => d.value),
                    backgroundColor: ['rgba(59, 130, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(16, 185, 129, 0.7)'],
                    borderWidth: 1,
                    borderColor: isDark() ? '#1f2937' : '#ffffff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { r: { grid: { color: gridColor() }, ticks: { display: false } } }
            }
        });
    }

    // ── 8. Status Berita (Thin Doughnut) ──────────────────────────────────
    const ctxBeritaStatus = document.getElementById('chartBeritaStatus');
    if (ctxBeritaStatus && beritaStatus.length > 0) {
        new Chart(ctxBeritaStatus, {
            type: 'doughnut',
            data: {
                labels: beritaStatus.map(d => d.label),
                datasets: [{
                    data: beritaStatus.map(d => d.value),
                    backgroundColor: ['#10b981','#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                cutout: '80%', // Make it thin
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // ── 9. Berita per Bulan (Bar) ─────────────────────────────────────────
    const ctxBeritaBulanan = document.getElementById('chartBeritaBulanan');
    if (ctxBeritaBulanan) {
        new Chart(ctxBeritaBulanan, {
            type: 'bar',
            data: {
                labels: beritaBulananLabels,
                datasets: [{
                    label: 'Berita Terbit',
                    data: beritaBulananData,
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: baseScales()
            }
        });
    }

    // ── 10. Status Pesan (Half Doughnut) ──────────────────────────────────
    const ctxPesanStatus = document.getElementById('chartPesanStatus');
    if (ctxPesanStatus && pesanStatus.length > 0) {
        new Chart(ctxPesanStatus, {
            type: 'doughnut',
            data: {
                labels: pesanStatus.map(d => d.label),
                datasets: [{
                    data: pesanStatus.map(d => d.value),
                    backgroundColor: ['#f43f5e','#10b981'],
                    borderWidth: 2,
                    borderColor: isDark() ? '#1f2937' : '#ffffff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                circumference: 180, // Half circle
                rotation: -90,      // Start from top-left
                cutout: '65%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // ── 11. Guru per Jenis Kelamin (Pie) ──────────────────────────────────
    const ctxGuru = document.getElementById('chartGuruGender');
    if (ctxGuru && guruGender.length > 0) {
        new Chart(ctxGuru, {
            type: 'pie',
            data: {
                labels: guruGender.map(d => d.label),
                datasets: [{
                    data: guruGender.map(d => d.value),
                    backgroundColor: ['#6366f1', '#ec4899'],
                    borderWidth: 2,
                    borderColor: isDark() ? '#1f2937' : '#ffffff',
                    hoverOffset: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // ── 12. Siswa per Agama (Radar) ───────────────────────────────────────
    const ctxAgama = document.getElementById('chartSiswaAgama');
    if (ctxAgama && siswaAgama.length > 0) {
        new Chart(ctxAgama, {
            type: 'radar',
            data: {
                labels: siswaAgama.map(d => d.label),
                datasets: [{
                    label: 'Siswa',
                    data: siswaAgama.map(d => d.value),
                    backgroundColor: 'rgba(6, 182, 212, 0.2)',
                    borderColor: '#06b6d4',
                    pointBackgroundColor: '#06b6d4',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { r: { grid: { color: gridColor() }, angleLines: { color: gridColor() }, ticks: { display: false } } }
            }
        });
    }

})();
</script>
@endpush
