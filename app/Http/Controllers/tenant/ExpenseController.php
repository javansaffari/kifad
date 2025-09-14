<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('tenant.expenses.index');
    }
}
