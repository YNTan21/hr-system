<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveType;
use App\Model\Leave;

class LeaveController extends Controller
{
    // Display the form
    public function create()
    {
        $users = User::all();
        $leaveTypes = LeaveType::all();
        return view('admin.leave.create', compact('users','leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required',
            'leave_type' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer',
            'reason' => 'nullable',
        ]);

        $fields = [
            'employee_name' => $request->employee_name,
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'number_of_days' => $request->number_of_days, 
            'placeholder' => $request->placeholder,     
        ];

        Leave::create($fields);
        // Auth::user()->posts()->store($fields);

        return redirect()->route('admin.leave.index')->with('success', 'Leave created successfully.');
    }
}
