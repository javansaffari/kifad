<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class CheckController extends Controller
{
    public function index()
    {
        return view('tenant.checks.index');
    }
}
