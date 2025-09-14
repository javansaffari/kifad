<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class LoanController extends Controller
{
    public function index()
    {
        return view('tenant.loans.index');
    }
}
