<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'users_total' => User::count(),
            'events_total' => class_exists(Event::class) ? Event::count() : 0,
        ];
        return view('admin.dashboard', compact('metrics'));
    }
}

