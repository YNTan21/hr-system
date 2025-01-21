<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;
use App\Models\AnnualLeaveBalance;
use App\Http\Controllers\AnnualLeaveBalanceController;

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

        // Check annual leave balance
        $annualLeaveBalance = AnnualLeaveBalance::where('user_id', $validatedData['user_id'])->first();
        if ($annualLeaveBalance && $annualLeaveBalance->annual_leave_balance < $validatedData['number_of_days']) {
            return redirect()->back()->with('error', 'Not enough annual leave balance to apply for this leave.');
        }

        // Create the leave request
        $leave = Leave::create([
            'user_id' => $validatedData['user_id'],
            'leave_type_id' => $validatedData['leave_type_id'],
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'number_of_days' => $validatedData['number_of_days'],
            'reason' => $validatedData['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave created successfully and is pending approval.');
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

        // Year filter with extended range
        $currentYear = date('Y');
        $year = $request->input('year', $currentYear); // Default to current year if not specified
        
        // Allow filtering for any year in the specified range
        $query->whereYear('from_date', $year);

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

        return view('admin.leave.index', compact('leaves', 'users', 'leaveTypes', 'currentYear'));
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
            'leave_type_id' => 'required|exists:leave_type,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $leave = Leave::findOrFail($id);
        
        // Check if this is an annual leave and the status is being changed to approved
        if ($leave->leaveType->code === 'AL' && 
            $validatedData['status'] === 'approved' && 
            $leave->status !== 'approved') {
            
            // Check annual leave balance
            $annualLeaveBalance = AnnualLeaveBalance::where('user_id', $validatedData['user_id'])->first();
            if ($annualLeaveBalance && $annualLeaveBalance->annual_leave_balance < $validatedData['number_of_days']) {
                return redirect()->back()->with('error', 'Not enough annual leave balance to approve this leave.');
            }
            
            // If approved, deduct from balance
            $annualLeaveController = new AnnualLeaveBalanceController();
            $deductionSuccess = $annualLeaveController->deductAnnualLeave(
                $validatedData['user_id'], 
                $validatedData['number_of_days']
            );
            
            if (!$deductionSuccess) {
                return redirect()->back()->with('error', 'Failed to update annual leave balance.');
            }
        }

        $leave->update($validatedData);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        
        // If it's an approved annual leave, restore the balance
        if ($leave->status === 'approved' && $leave->leaveType->code === 'AL') {
            $annualLeaveBalance = AnnualLeaveBalance::where('user_id', $leave->user_id)->first();
            if ($annualLeaveBalance) {
                $annualLeaveBalance->annual_leave_balance += $leave->number_of_days;
                $annualLeaveBalance->save();
            }
        }
        
        $leave->delete();

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave request deleted successfully.');
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

    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        
        // Set the status explicitly to 'approved'
        $status = 'approved';
        
        // Check if it's an annual leave
        if ($leave->leaveType->code === 'AL') {
            $annualLeaveController = new AnnualLeaveBalanceController();
            
            // Try to deduct the leave days
            $deductionSuccess = $annualLeaveController->deductAnnualLeave(
                $leave->user_id, 
                $leave->number_of_days
            );
            
            if (!$deductionSuccess) {
                return back()->with('error', 'Insufficient annual leave balance.');
            }
        }

        $leave->status = $status;
        $leave->save();

        return back()->with('success', 'Leave status updated successfully.');
    }

    public function reject(Leave $leave)
    {
        $leave->status = 'rejected';
        $leave->save();
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

    public function export()
    {
        try {
            $leaves = Leave::with(['user', 'leaveType'])->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=leave_records.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
                'Employee Name',
                'Leave Type',
                'From Date',
                'To Date',
                'Number of Days',
                'Reason',
                'Status',
                'Applied Date'
            ]);

            // Write data
            foreach ($leaves as $leave) {
                fputcsv($output, [
                    $leave->user->username,
                    $leave->leaveType->leave_type,
                    date('d/m/Y', strtotime($leave->from_date)),
                    date('d/m/Y', strtotime($leave->to_date)),
                    $leave->number_of_days,
                    $leave->reason,
                    ucfirst($leave->status),
                    date('d/m/Y', strtotime($leave->created_at))
                ]);
            }

            fclose($output);

            return response()->stream(
                function() {
                    // Data has already been written to output
                },
                200,
                $headers
            );

        } catch (\Exception $e) {
            \Log::error('Export Error', [
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Failed to export data']);
        }
    }
}