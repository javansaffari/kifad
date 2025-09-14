<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class AccountingController extends Controller
{
    public function index()
    {
        return view('tenant.accounting.index');
    }
}
