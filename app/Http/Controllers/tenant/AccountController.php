<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        return view('tenant.accounts.index');
    }
}
