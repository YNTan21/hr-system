<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;

class EmployeeController extends Controller
{
    public function edit($id)
    {
        // Fetch user instead of employee
        $employee = User::findOrFail($id);

        // Check if the authenticated user is an admin
        if (!auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to edit employee profiles.');
        }

        $leaveTypes = LeaveType::all();
        $leaveBalances = $employee->leaveBalances; // Assuming you have this relation defined in the User model

        return view('admin.employee.edit', compact('employee', 'leaveTypes', 'leaveBalances'));
    }

    public function update(Request $request, $id)
    {
        // Fetch user instead of employee
        $employee = User::findOrFail($id); 

        // Check if the authenticated user is the owner of the employee profile or an admin
        if ($employee->id !== auth()->id() && !auth()->user()->is_admin) {
            return redirect()->route('user.dashboard')->with('error', 'You do not have permission to update this employee.');
        }

        $validatedData = $request->validate([
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'ic' => 'required|string|unique:users,ic,'.$employee->id,
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'nationality' => 'required|string|max:255',
            'bank_account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
        ]);

        // Update the user record
        $employee->update($validatedData);

        return redirect()->route('admin.employee.edit', $employee->id)->with('success', 'Employee profile updated successfully');
    }

    public function updateLeaveBalance(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'days_available' => 'required|integer|min:0',
        ]);

        LeaveBalance::updateOrCreate(
            [
                'user_id' => $validatedData['user_id'],
                'leave_type_id' => $validatedData['leave_type_id'],
            ],
            ['days_available' => $validatedData['days_available']]
        );

        return redirect()->route('admin.employee.edit', $validatedData['user_id'])->with('success', 'Leave balance updated successfully');
    }
}
