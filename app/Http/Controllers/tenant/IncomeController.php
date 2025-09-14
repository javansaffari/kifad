<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class IncomeController extends Controller
{
    public function index()
    {
        return view('tenant.income.index');
    }
}
