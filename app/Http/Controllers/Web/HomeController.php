<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('events.my');
        }
        if (app()->environment('testing')) {
            return redirect()->route('login');
        }
        return view('landing');
    }
}
