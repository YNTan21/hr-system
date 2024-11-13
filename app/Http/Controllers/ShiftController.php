<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ShiftController extends Controller
{
    public function shiftList()
    {
        $employees = User::all();
        return view('admin.timetable.shiftlist', compact('employees'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Add your logic to save the shift assignment
        // Example:
        // Shift::create($validated);

        return redirect()->back()->with('success', 'Shift assigned successfully');
    }
}