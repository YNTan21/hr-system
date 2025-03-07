<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'is_admin' => 'boolean',
            ]);

            // Generate a random password
            $password = Str::random(10);

            \Log::info('Starting user creation process');

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'is_admin' => $validated['is_admin'] ?? false,
            ]);

            \Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            // Send test email first
            \Log::info('Attempting to send test email');
            \Mail::raw('Test email from HR System', function($message) use ($user) {
                $message->to($user->email)
                       ->subject('Test Email');
            });
            \Log::info('Test email sent successfully');

            // Then send the actual notification
            \Log::info('Attempting to send welcome notification');
            $user->notify(new NewAccountCreated($password));
            \Log::info('Welcome notification sent successfully');

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully. Login credentials have been sent to their email.');

        } catch (\Exception $e) {
            \Log::error('Error in user creation process: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
} 