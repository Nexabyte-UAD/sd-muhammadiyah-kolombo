<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

/**
 * Controller ActivityLogController
 * 
 * Mengelola riwayat audit trail (log aktivitas) admin untuk keperluan keamanan dan pelacakan
 * tindakan operasional di website SD Muhammadiyah Komplek Kolombo.
 */
class ActivityLogController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas secara terpaginasi (20 item per halaman).
     * 
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $activities = ActivityLog::latest()->paginate(20);

        return view('admin.activity-logs.index', compact('activities'));
    }
}
