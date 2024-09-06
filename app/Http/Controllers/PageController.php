<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('dashboard.index');
    }

    public function leaveType_create()
    {
        return view('leaveType.create');
    }
}
