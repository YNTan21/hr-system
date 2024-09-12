<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function user_dashboard()
    {
        return view('user.dashboard');
    }

    public function open_menu()
    {
        $user = Auth::user();
        return view('admin/dashboard',['username' => $user->username, 'email' => $email->email]);
    }

    public function leaveType_create()
    {
        return view('admin.leaveType.create');
    }
}
