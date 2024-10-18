<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;

class LeaveController extends Controller
{
    use AuthorizesRequests;
    
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
            'leave_type_id' => 'required|exists:leave_type,id', 
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
        $query = Leave::with(['user', 'leaveType']);

        // Apply filters if they are present in the request
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('filter_date')) {
            $filterDate = $request->filter_date;
            $query->where(function($q) use ($filterDate) {
                $q->where('from_date', '<=', $filterDate)
                  ->where('to_date', '>=', $filterDate);
            });
        }

        // Order by created_at in descending order to show newest leaves first
        $query->orderBy('created_at', 'desc');

        // Paginate the results
        $leaves = $query->paginate(10);

        // Get all users and leave types for the filters
        $users = User::all();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.index', compact('leaves', 'users', 'leaveTypes'));
    }

    public function edit($id)
    {
        
        $leave = Leave::findOrFail($id);
        Gate::authorize('update', $leave);

        if ($leave->user_id !== auth()->id() && !auth()->user()->is_admin) {
            return redirect()->route('admin.leave.index')->with('error', 'You do not have permission to edit this leave.');
        }

        $this->authorize('update', $leave);  

        $users = User::all();
        $leaveTypes = LeaveType::all(); 

        return view('admin.leave.edit', compact('leave', 'users', 'leaveTypes'));
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        // Check if the authenticated user is the owner of the leave  
        if ($leave->user_id !== auth()->id() && !auth()->user()->is_admin) {
            return redirect()->route('admin.leave.index')->with('error', 'You do not have permission to update this leave.');
        }

        $this->authorize('update', $leave);

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id', 
            'leave_type_id' => 'required|exists:leave_type,id', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave->update($validatedData);

        return redirect()->route('admin.leave.index')->with('success', 'Leave updated successfully.');
    }

    public function processLeaves(Request $request)
    {
        $query = Leave::with(['user', 'leaveType'])->where('status', 'pending');

        // Apply filters if they are present in the request
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('filter_date')) {
            $filterDate = $request->filter_date;
            $query->where(function($q) use ($filterDate) {
                $q->where('from_date', '<=', $filterDate)
                  ->where('to_date', '>=', $filterDate);
            });
        }

        // Order by created_at in descending order to show newest leaves first
        $query->orderBy('created_at', 'desc');

        // Paginate the results
        $leaves = $query->paginate(10);

        // Get all users and leave types for the filters
        $users = User::all();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.process', compact('leaves', 'users', 'leaveTypes'));
    }

    public function approve(Leave $leave)
    {
        $leave->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Leave approved successfully.');
    }

    public function reject(Leave $leave)
    {
        $leave->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Leave rejected successfully.');
    }

    public function show(Leave $leave)
    {
        return view('admin.leave.show', compact('leave'));
    }

    public function leaveBalance(Request $request)
    {
        $query = Leave::with(['user', 'leaveType']);

        // Apply filters if they are present in the request
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate the results
        $leaves = $query->paginate(10);

        // Get all users and leave types for the filters
        $users = User::all();

        return view('admin.leave.leave-balance', compact('leaves', 'users'));
    }
}