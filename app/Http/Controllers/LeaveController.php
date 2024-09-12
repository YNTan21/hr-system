<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;

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
            'user_id' => 'required', 
            'leave_type_id' => 'required', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer',
            'reason' => 'nullable',
        ]);

        $fields = [
            'user_id' => $request->user_id, 
            'leave_type_id' => $request->leave_type_id, 
            'from_date' => $request->from_date, 
            'to_date' => $request->to_date, 
            'number_of_days' => $request->number_of_days, 
            'reason' => $request->reason, 
            'status' => 'pending', 
        ];

        Leave::create($fields);
        // Auth::user()->posts()->store($fields);

        return redirect()->route('admin.leave.index')->with('success', 'Leave created successfully.');
    }

    public function index(Request $request)
    {
        // Initialize the query builder
        $query = Leave::with('user', 'leaveType');

        // Filter by employee name
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by leave type
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('from_date', [$request->from_date, $request->to_date]);
        }

        // Execute the query with pagination
        $leaves = $query->paginate(10);

        // $leaves = Leave::with('user', 'leaveType')->paginate(10);

        $users = User::all();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.index', compact('leaves', 'users', 'leaveTypes'));
    }
}
