<?php

namespace App\Http\Controllers\central;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    public function index()
    {
        return view('central.support.index');
    }
}
