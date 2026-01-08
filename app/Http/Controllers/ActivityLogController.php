<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user', 'subject')
            ->latest()
            ->paginate(20);

        return view('tenant.activity-logs.index', compact('logs'));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('tenant.activity-logs.show', compact('activityLog'));
    }
}
