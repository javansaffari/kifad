<?php

namespace App\Http\Controllers\central;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        return view('central.help.index');
    }
}
