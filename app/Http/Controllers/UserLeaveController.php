<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;

class UserLeaveController extends Controller
{
    use AuthorizesRequests;
    
    public function create()
    {
        $leaveTypes = LeaveType::all();
        $user = auth()->user(); // Get the authenticated user
        return view('user.leave.create', compact('leaveTypes', 'user'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'leave_type_id' => 'required|exists:leave_type,id', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $leave = Leave::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $validatedData['leave_type_id'],
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'number_of_days' => $validatedData['number_of_days'],
            'reason' => $validatedData['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('user.leave.index')->with('success', 'Leave request submitted successfully.');
    }

    public function index(Request $request)
    {
        $query = Leave::where('user_id', auth()->id())->with(['leaveType', 'user']);

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
        $leaveTypes = LeaveType::all();

        return view('user.leave.index', compact('leaves', 'leaveTypes'));
    }

    public function edit($id)
    {
        $leave = Leave::where('user_id', auth()->id())->with('user')->findOrFail($id);
        
        if ($leave->status !== 'pending') {
            return redirect()->route('user.leave.index')->with('error', 'You can only edit pending leave requests.');
        }

        $leaveTypes = LeaveType::all(); 

        return view('user.leave.edit', compact('leave', 'leaveTypes'));
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::where('user_id', auth()->id())->findOrFail($id);

        if ($leave->status !== 'pending') {
            return redirect()->route('user.leave.index')->with('error', 'You can only update pending leave requests.');
        }

        $validatedData = $request->validate([
            'leave_type_id' => 'required|exists:leave_type,id', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $leave->update($validatedData);

        return redirect()->route('user.leave.index')->with('success', 'Leave request updated successfully.');
    }
}