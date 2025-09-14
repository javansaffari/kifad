<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;

class PersonController extends Controller
{
    public function index()
    {
        return view('tenant.people.index');
    }
}
