<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $activities = ActivityLog::latest()->paginate(20);

        return view('admin.activity-logs.index', compact('activities'));
    }
}
