<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model PageView
 * 
 * Merepresentasikan satu catatan kunjungan halaman publik website
 * SD Muhammadiyah Komplek Kolombo. Digunakan oleh Dashboard Analitik Admin
 * untuk menampilkan statistik trafik dan popularitas halaman.
 */
class PageView extends Model
{
    // Tidak menggunakan updated_at karena kunjungan bersifat immutable
    const UPDATED_AT = null;
    const CREATED_AT = 'visited_at';

    protected $table = 'page_views';

    protected $fillable = [
        'page',        // Path URL halaman (/berita, /guru, dll.)
        'page_label',  // Label tampilan halaman
        'ip_address',  // IP pengunjung
        'user_agent',  // Browser/device info
        'visited_at',  // Timestamp kunjungan
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Mapping path URL ke label yang mudah dibaca.
     */
    public static array $pageLabels = [
        '/'               => 'Beranda',
        '/sambutan'       => 'Kata Sambutan',
        '/tentang'        => 'Tentang Sekolah',
        '/visi-misi'      => 'Visi & Misi',
        '/akreditasi'     => 'Akreditasi',
        '/guru'           => 'Guru & Staf',
        '/prestasi'       => 'Prestasi',
        '/ekstrakurikuler'=> 'Ekstrakurikuler',
        '/siswa'          => 'Data Siswa',
        '/kelas'          => 'Data Kelas',
        '/alumni'         => 'Alumni',
        '/berita'         => 'Berita',
    ];

    /**
     * Resolve label halaman dari path URL.
     */
    public static function resolveLabel(string $path): string
    {
        // Berita detail
        if (str_starts_with($path, '/berita/')) {
            return 'Detail Berita';
        }
        return static::$pageLabels[$path] ?? $path;
    }

    /**
     * Hitung total kunjungan hari ini.
     */
    public static function todayCount(): int
    {
        return static::whereDate('visited_at', today())->count();
    }

    /**
     * Hitung total kunjungan dalam N hari terakhir.
     */
    public static function lastDaysCount(int $days = 30): int
    {
        return static::where('visited_at', '>=', now()->subDays($days))->count();
    }

    /**
     * Hitung unique visitor (berdasarkan IP) dalam N hari terakhir.
     */
    public static function uniqueVisitors(int $days = 30): int
    {
        return static::where('visited_at', '>=', now()->subDays($days))
            ->whereNotNull('ip_address')
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Data tren kunjungan per hari untuk N hari terakhir.
     * Mengembalikan array ['label' => ['Senin', ...], 'data' => [12, ...]]
     */
    public static function dailyTrend(int $days = 30): array
    {
        $rows = static::select(
                DB::raw('DATE(visited_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('visited_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->isoFormat('D MMM');
            $data[]   = $rows->get($date)?->total ?? 0;
        }

        return compact('labels', 'data');
    }

    /**
     * Top halaman paling banyak dikunjungi.
     */
    public static function topPages(int $limit = 8, int $days = 30): \Illuminate\Support\Collection
    {
        return static::select('page_label', DB::raw('COUNT(*) as total'))
            ->where('visited_at', '>=', now()->subDays($days))
            ->whereNotNull('page_label')
            ->groupBy('page_label')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
