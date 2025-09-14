<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class InvestmentController extends Controller
{
    public function index()
    {
        return view('tenant.investments.index');
    }
}
