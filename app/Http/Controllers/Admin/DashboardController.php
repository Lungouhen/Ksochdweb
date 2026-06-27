<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_members' => 0,
            'active_members' => 0,
            'pending_applications' => 0,
            'total_donations' => 0,
            'active_campaigns' => 0,
            'upcoming_events' => 0,
            'active_volunteers' => 0,
            'recent_posts' => 0,
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
