<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function saveRecord(Request $request)
    {
        $request->validate([
            'shift_name' => 'required',
            'min_start_time' => 'required',
            'start_time' => 'required',
            'max_start_time' => 'required',
            'end_time' => 'required',
        ]);

        Shift::create([
            'shift_name' => $request->shift_name,
            'min_start_time' => $request->min_start_time,
            'start_time' => $request->start_time,
            'max_start_time' => $request->max_start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->back()->with('success', 'Shift added successfully');
    }
}