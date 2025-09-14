<?php

namespace App\Http\Controllers\central;

use App\Http\Controllers\Controller;

class BillingController extends Controller
{
    public function index()
    {
        return view('central.billing.index');
    }
}
