<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('admin.leaveType.index', ['leaveTypes' => $leaveTypes]);
    }
    
    public function store(Request $request)
    {
        
        $request->validate([
            'leaveType' => 'required|string|max:255',
            'leaveCode' => 'required|string|max:8',
            'status' => 'required|in:active,inactive'
        ]);

        $fields = [
            'leave_type' => $request->leaveType,
            'code' => $request->leaveCode,
            'status' => $request->status,
            'deduct_annual_leave' => $request->has('deductAnnualLeave'),
        ];

        LeaveType::create($fields);
        // Auth::user()->posts()->store($fields);

        return redirect()->route('admin.leaveType.index')->with('success', 'Leave Type created successfully.');
    }

    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('admin.leaveType.edit', compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        $request -> validate([
            'leaveType' => 'required|string|max:255',
            'leaveCode' => 'required|string|max:8',
            'status' => 'required|in:active,inactive'
        ]);

        $leaveType = LeaveType::findOrFail($id);

        $leaveType->update([
            'leave_type' => $request->leaveType,
            'code' => $request->leaveCode,
            'status' => $request->status,
            'deduct_annual_leave' => $request->has('deductAnnualLeave'),
        ]);

        return redirect()->route('admin.leaveType.index')->with('success', 'Leave Type updated successfully.');
    }

    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->delete();

        return redirect()->route('admin.leaveType.index')->with('success', 'Leave Type deleted successfully.');
    }
}
