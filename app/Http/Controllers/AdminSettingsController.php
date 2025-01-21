<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class AdminSettingsController extends Controller
{
    public function showPinSettings()
    {
        return view('admin.settings.pin');
    }

    public function updatePin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|string|digits:6',
            'new_pin' => 'required|string|digits:6',
            'confirm_pin' => 'required|string|same:new_pin',
        ]);

        // Get current PIN from settings
        $setting = Setting::where('key', 'system_pin')->first();
        
        // Verify current PIN
        if ($request->current_pin !== $setting->value) {
            return back()->withErrors(['current_pin' => 'The current PIN is incorrect.']);
        }

        // Update PIN
        $setting->value = $request->new_pin;
        $setting->save();

        return back()->with('success', 'PIN has been updated successfully.');
    }
}
