<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin == 1) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('user.dashboard');
    }

    // Add this method to override the default logout behavior
    protected function loggedOut(Request $request)
    {
        return redirect()->route('login');
    }
}