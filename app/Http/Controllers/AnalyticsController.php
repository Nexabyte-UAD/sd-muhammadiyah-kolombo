<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Ekstrakurikuler;
use App\Models\GuruStaff;
use App\Models\Kelas;
use App\Models\PageView;
use App\Models\Pesan;
use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controller AnalyticsController
 * 
 * Mengelola halaman Analitik pada panel admin. Menyajikan data statistik
 * pengunjung website (dari tabel page_views) dan insight data internal sekolah
 * (siswa, guru, berita, prestasi, pesan) dalam bentuk chart dan ringkasan.
 */
class AnalyticsController extends Controller
{
    /**
     * Menampilkan halaman Analitik beserta semua data chart.
     */
    public function index(): View
    {
        // ── Statistik Pengunjung (Google Analytics 4) ─────────────────────────
        $pengunjungHariIni   = 0;
        $pengunjungBulanIni  = 0;
        $uniqueVisitor30Hari = 0;
        $totalAllTime        = 0;
        $trendHarian         = ['labels' => [], 'data' => []];
        $topHalaman          = collect();

        try {
            $period30 = \Spatie\Analytics\Period::days(30);
            
            // Tren harian 30 hari
            $dailyAnalytics = \Spatie\Analytics\Facades\Analytics::fetchTotalVisitorsAndPageViews($period30);
            
            // Ambil data hari ini (berdasarkan tanggal hari ini)
            $today = \Carbon\Carbon::today();
            $todayData = $dailyAnalytics->first(function($item) use ($today) {
                return isset($item['date']) && $item['date']->isSameDay($today);
            });
            $pengunjungHariIni = $todayData ? ($todayData['screenPageViews'] ?? 0) : 0;

            $pengunjungBulanIni = $dailyAnalytics->sum('screenPageViews');

            // Unique Visitors (activeUsers) & Total Kunjungan (screenPageViews) 30 Hari (agregat total)
            $totals30 = \Spatie\Analytics\Facades\Analytics::get($period30, ['activeUsers', 'screenPageViews']);
            $uniqueVisitor30Hari = $totals30->first()['activeUsers'] ?? $dailyAnalytics->sum('activeUsers');

            // Total Kunjungan All Time (Dibatasi 1 Tahun/12 Bulan karena kebijakan retensi data GA4)
            $totalsYear = \Spatie\Analytics\Facades\Analytics::get(\Spatie\Analytics\Period::months(12), ['screenPageViews']);
            $totalAllTime = $totalsYear->first()['screenPageViews'] ?? 0;

            // Format data trendHarian untuk Chart.js
            $trendHarian = [
                'labels' => $dailyAnalytics->pluck('date')->map(fn($d) => $d->format('d M'))->toArray(),
                'data'   => $dailyAnalytics->pluck('screenPageViews')->toArray(),
            ];

            // Top 8 halaman paling banyak dikunjungi (30 hari)
            $topPagesData = \Spatie\Analytics\Facades\Analytics::fetchMostVisitedPages($period30, 8);
            $topHalaman = $topPagesData->map(function($page) {
                return (object)[
                    'page_label' => $page['pageTitle'] ?: $page['url'],
                    'total'      => $page['screenPageViews'],
                ];
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GA4 Fetch Error: ' . $e->getMessage());
        }

        // ── Statistik Siswa ───────────────────────────────────────────────────

        // Distribusi siswa aktif per kelas
        $siswaPerKelas = Kelas::withCount(['siswas' => fn($q) => $q->where('status', 'aktif')])
            ->having('siswas_count', '>', 0)
            ->orderBy('urutan')
            ->get()
            ->map(fn($k) => ['label' => $k->tingkat, 'value' => $k->siswas_count]);

        // Komposisi jenis kelamin siswa aktif
        $siswaGender = Siswa::aktif()
            ->select('jenis_kelamin', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get()
            ->map(fn($row) => [
                'label' => match ($row->jenis_kelamin) {
                    'laki_laki'  => 'Laki-laki',
                    'perempuan'  => 'Perempuan',
                    default      => ucfirst($row->jenis_kelamin ?? 'Lainnya'),
                },
                'value' => $row->total,
            ]);

        // Status siswa: aktif, alumni, keluar
        $siswaStatus = Siswa::withTrashed()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn($row) => [
                'label' => match ($row->status) {
                    'aktif'  => 'Siswa Aktif',
                    'alumni' => 'Alumni',
                    'keluar' => 'Keluar/Pindah',
                    default  => ucfirst($row->status ?? '-'),
                },
                'value' => $row->total,
            ]);

        // Tren penerimaan siswa per tahun masuk (semua status)
        $siswaTahunan = Siswa::withTrashed()
            ->select('tahun_masuk', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tahun_masuk')
            ->groupBy('tahun_masuk')
            ->orderBy('tahun_masuk')
            ->get()
            ->map(fn($row) => ['label' => (string) $row->tahun_masuk, 'value' => $row->total]);

        // Siswa aktif per agama
        $siswaAgama = Siswa::aktif()
            ->select('agama', DB::raw('COUNT(*) as total'))
            ->whereNotNull('agama')
            ->groupBy('agama')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => ['label' => $row->agama ?? '-', 'value' => $row->total]);

        // ── Statistik Prestasi ────────────────────────────────────────────────

        // Prestasi per kategori
        $prestasiKategori = Prestasi::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->get()
            ->map(fn($row) => [
                'label' => Prestasi::KATEGORI[$row->kategori] ?? ucfirst($row->kategori ?? 'Lainnya'),
                'value' => $row->total,
            ]);

        // Tren prestasi per tahun
        $prestasiTahunan = Prestasi::select(
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('tanggal')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get()
            ->map(fn($row) => ['label' => (string) $row->tahun, 'value' => $row->total]);

        // ── Statistik Berita ──────────────────────────────────────────────────

        // Berita published vs draft
        $beritaStatus = Berita::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn($row) => [
                'label' => $row->status === 'published' ? 'Terbit' : 'Draft',
                'value' => $row->total,
            ]);

        // Tren berita terbit per bulan (12 bulan terakhir)
        $beritaBulanan = Berita::select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Lengkapi 12 bulan dengan 0 jika tidak ada berita
        $beritaBulananLabels = [];
        $beritaBulananData   = [];
        $beritaMap = $beritaBulanan->keyBy('bulan');
        for ($i = 11; $i >= 0; $i--) {
            $key                   = now()->subMonths($i)->format('Y-m');
            $beritaBulananLabels[] = now()->subMonths($i)->isoFormat('MMM YY');
            $beritaBulananData[]   = $beritaMap->get($key)?->total ?? 0;
        }

        // ── Statistik Pesan ───────────────────────────────────────────────────

        // Tren pesan masuk per bulan (12 bulan terakhir)
        $pesanBulanan = Pesan::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $pesanBulananLabels = [];
        $pesanBulananData   = [];
        $pesanMap = $pesanBulanan->keyBy('bulan');
        for ($i = 11; $i >= 0; $i--) {
            $key                  = now()->subMonths($i)->format('Y-m');
            $pesanBulananLabels[] = now()->subMonths($i)->isoFormat('MMM YY');
            $pesanBulananData[]   = $pesanMap->get($key)?->total ?? 0;
        }

        // Pesan dibaca vs belum
        $pesanStatus = collect([
            ['label' => 'Belum Dibaca', 'value' => Pesan::whereNull('read_at')->count()],
            ['label' => 'Sudah Dibaca', 'value' => Pesan::whereNotNull('read_at')->count()],
        ]);

        // ── Ringkasan Guru ────────────────────────────────────────────────────
        $guruGender = GuruStaff::select('jenis_kelamin', DB::raw('COUNT(*) as total'))
            ->whereNotNull('jenis_kelamin')
            ->groupBy('jenis_kelamin')
            ->get()
            ->map(fn($row) => [
                'label' => $row->jenis_kelamin === 'laki_laki' ? 'Laki-laki' : 'Perempuan',
                'value' => $row->total,
            ]);

        return view('admin.analitik.index', compact(
            // Pengunjung
            'pengunjungHariIni', 'pengunjungBulanIni', 'uniqueVisitor30Hari', 'totalAllTime',
            'trendHarian', 'topHalaman',
            // Siswa
            'siswaPerKelas', 'siswaGender', 'siswaStatus', 'siswaTahunan', 'siswaAgama',
            // Prestasi
            'prestasiKategori', 'prestasiTahunan',
            // Berita
            'beritaStatus', 'beritaBulananLabels', 'beritaBulananData',
            // Pesan
            'pesanBulananLabels', 'pesanBulananData', 'pesanStatus',
            // Guru
            'guruGender',
        ));
    }
}
