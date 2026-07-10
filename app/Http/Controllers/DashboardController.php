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

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'countGuru' => GuruStaff::count(),
            'countBerita' => Berita::count(),
            'countPesan' => Pesan::count(),
            'countSiswa' => Siswa::aktif()->count(),
            'countAlumni' => Siswa::alumni()->count(),
            'latestBerita' => Berita::latest()->take(4)->get(),
            'latestPesan' => Pesan::latest()->take(3)->get(),
            'recentActivities' => ActivityLog::latest()->take(3)->get(),
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
