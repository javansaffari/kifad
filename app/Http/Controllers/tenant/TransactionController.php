<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
        return view('tenant.transactions.index');
    }
}
