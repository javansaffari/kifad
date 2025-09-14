<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('tenant.dashboard.index');
    }
}
