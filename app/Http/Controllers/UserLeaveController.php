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
    
    public function create(Request $request)
    {
        $leaveTypes = LeaveType::all();
        $user = auth()->user();
        
        // Pre-select annual leave type if specified
        $selectedLeaveType = null;
        if ($request->type === 'annual') {
            $selectedLeaveType = LeaveType::where('leave_type', 'LIKE', '%annual%')->first()?->id;
        }
        
        return view('user.leave.create', compact('leaveTypes', 'user', 'selectedLeaveType'));
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
        $query = Leave::where('user_id', auth()->id())
                     ->with(['leaveType', 'user']);

        // Apply month filter if selected
        if ($request->filled('month')) {
            $query->whereMonth('from_date', $request->month);
        }

        // Apply year filter if selected
        if ($request->filled('year')) {
            $query->whereYear('from_date', $request->year);
        }

        $leaves = $query->orderBy('created_at', 'desc')
                       ->paginate(10);

        // Eager load the annual leave balance
        $user = auth()->user()->load('annualLeaveBalance');

        return view('user.leave.index', compact('leaves', 'user'));
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

    public function export()
    {
        try {
            $leaves = Leave::where('user_id', auth()->id())
                ->with(['leaveType'])
                ->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=my_leave_history.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
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

    public function destroy($id)
    {
        $leave = Leave::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $leave->delete();

        return redirect()->route('user.leave.index')
            ->with('success', 'Leave request deleted successfully.');
    }
}