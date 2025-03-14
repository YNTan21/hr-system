<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    // register
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'min:4', 'confirmed'],
        ]);

        // create a user
        $user = User::create($fields);

        // logged in
        Auth::login($user);

        // redirect
        return redirect('/user/dashboard')->with('success', 'account is created successfully');
    }

    // login
    public function login(Request $request)
    {
        // validation
        $fields = $request->validate([
            'email' => ['required', 'max:255'],
            'password' => ['required']
        ]);
        
        // login
        if(Auth::attempt($fields, $request->remember))
        {
            $user = Auth::user();

            // Regenerate session for security
            $request->session()->regenerate();

            if($user->is_admin)
            {
                return redirect()->intended('/admin/dashboard');
            }
            else
            {
                return redirect()->intended('/user/dashboard');
            }
        }
        else
        {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
        }
    }

    public function logout(Request $request)
    {
        // logout the user
        Auth::logout();

        // invalicate the user's session
        $request->session()->invalidate();

        // regenerate csrf token
        $request->session()->regenerateToken();

        // redirect
        return redirect('/login');
    }
}
