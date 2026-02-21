<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class ChequesController extends Controller
{
    public function index()
    {
        return view('tenant.cheques.index');
    }
}
