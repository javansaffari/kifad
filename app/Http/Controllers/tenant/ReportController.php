<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('tenant.reports.index');
    }
}
