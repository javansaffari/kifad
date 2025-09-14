<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class DebtController extends Controller
{
    public function index()
    {
        return view('tenant.debts.index');
    }
}
