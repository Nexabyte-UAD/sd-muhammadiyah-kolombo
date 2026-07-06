<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Berita;
use App\Models\GuruStaff;
use App\Models\Pesan;
use App\Models\Siswa;
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
            'latestBerita' => Berita::latest()->take(4)->get(),
            'latestPesan' => Pesan::latest()->take(3)->get(),
            'recentActivities' => ActivityLog::latest()->take(5)->get(),
        ]);
    }
}
