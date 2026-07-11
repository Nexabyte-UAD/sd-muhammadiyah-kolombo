<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Berita;
use App\Models\GuruStaff;
use App\Models\Pesan;
use App\Models\Siswa;
use App\Models\Ekstrakurikuler;
use App\Models\Prestasi;
use Illuminate\View\View;

/**
 * Controller DashboardController
 * 
 * Mengelola dasbor utama panel administrator (admin panel), menyajikan data statistik,
 * ringkasan pesan masuk, log aktivitas sistem, serta kartu peringatan/alert status data
 * (misal: siswa tanpa kelas, data guru belum lengkap, pesan belum dibaca).
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan dasbor panel administrator beserta statistik lengkap.
     * 
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('dashboard', [
            // Statistik kuantitas utama
            'countGuru' => GuruStaff::count(),
            'countBerita' => Berita::count(),
            'countPesan' => Pesan::count(),
            'countSiswa' => Siswa::aktif()->count(),
            'countAlumni' => Siswa::alumni()->count(),

            // Data ringkasan terbaru
            'latestBerita' => Berita::latest()->take(4)->get(),
            'latestPesan' => Pesan::latest()->take(3)->get(),
            'recentActivities' => ActivityLog::latest()->take(3)->get(),

            // Notifikasi/Pemberitahuan kualitas & kelengkapan data
            'countSiswaTanpaKelas' => Siswa::aktif()
                ->whereNull('kelas_id')
                ->where(fn ($query) => $query->whereNull('kelas')->orWhere('kelas', ''))
                ->count(),
            'countGuruBelumLengkap' => GuruStaff::where(function ($query) {
                foreach (['foto', 'jabatan', 'jenis_kelamin', 'pendidikan_terakhir'] as $field) {
                    $query->orWhereNull($field)->orWhere($field, '');
                }
            })->count(),
            'countPesanBelumDibaca' => Pesan::whereNull('read_at')->count(),
            'countBeritaDraft' => Berita::where('status', 'draft')->count(),
            'countEkskulBelumLengkap' => Ekstrakurikuler::where(function($query) {
                $query->whereNull('pembina')->orWhere('pembina', '')
                       ->orWhereNull('jadwal')->orWhere('jadwal', '');
            })->count(),
            'countPrestasiTanpaFoto' => Prestasi::whereNull('gambar')->orWhere('gambar', '')->count(),
        ]);
    }
}
