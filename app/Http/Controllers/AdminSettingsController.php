<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use App\Models\PinHistory;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    public function showPinSettings()
    {
        $currentPin = null;
        $pinHistories = [];
        if (Auth::user() && Auth::user()->is_admin) {
            $setting = Setting::where('key', 'system_pin')->first();
            $currentPin = $setting ? $setting->value : null; // Show actual PIN
            $pinHistories = PinHistory::orderBy('created_at', 'desc')->take(10)->get();
        }
        return view('admin.settings.pin', compact('currentPin', 'pinHistories'));
    }

    public function updatePin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|string|digits:6',
            'new_pin' => 'required|string|digits:6',
            'confirm_pin' => 'required|string|same:new_pin',
        ]);

        $setting = Setting::where('key', 'system_pin')->first();
        if (!$setting) {
            return back()->withErrors(['current_pin' => 'System PIN not configured.']);
        }
        // Verify current PIN (plain text)
        if ($request->current_pin !== $setting->value) {
            return back()->withErrors(['current_pin' => 'The current PIN is incorrect.']);
        }
        // Store old PIN in history (plain text)
        PinHistory::create([
            'pin' => $setting->value,
            'changed_by' => Auth::id(),
        ]);
        // Update PIN (plain text)
        $setting->value = $request->new_pin;
        $setting->save();
        return back()->with('success', 'PIN has been updated successfully.');
    }
}
