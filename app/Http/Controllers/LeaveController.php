<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;

class LeaveController extends Controller
{
    public function create()
    {
        $users = User::all();
        $leaveTypes = LeaveType::all();
        return view('admin.leave.create', compact('users','leaveTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id', 
            'leave_type_id' => 'required|exists:leave_types,id', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $leave = Leave::create([
            'user_id' => $validatedData['user_id'],
            'leave_type_id' => $validatedData['leave_type_id'],
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'number_of_days' => $validatedData['number_of_days'],
            'reason' => $validatedData['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave created successfully.');
    }

    public function index(Request $request)
    {
        $query = Leave::with('user', 'leaveType');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('from_date', [$request->from_date, $request->to_date]);
        }

        $leaves = $query->paginate(10);

        $users = User::all();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.index', compact('leaves', 'users', 'leaveTypes'));
    }

    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        $users = User::all();
        $leaveTypes = LeaveType::all();
        return view('admin.leave.edit', compact('leave', 'users', 'leaveTypes'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id', 
            'leave_type_id' => 'required|exists:leave_types,id', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $leave = Leave::findOrFail($id);

        $leave->update([
            'user_id' => $validatedData['user_id'],
            'leave_type_id' => $validatedData['leave_type_id'],
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'number_of_days' => $validatedData['number_of_days'],
            'reason' => $validatedData['reason'],
            'status' => 'pending', // You might want to handle this differently
        ]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave updated successfully.');
    }
}