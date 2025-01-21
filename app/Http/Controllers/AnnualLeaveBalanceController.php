<?php

namespace App\Http\Controllers;

use App\Models\AnnualLeaveBalance;
use App\Models\User;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class AnnualLeaveBalanceController extends Controller
{
    // Display a listing of the annual leave balances
    public function index(Request $request)
    {
        // Retrieve all users
        $users = User::all();
        
        // Retrieve leave balances, optionally applying filters
        $query = AnnualLeaveBalance::with('user');

        // Example filter by user name
        if ($request->has('name') && $request->input('name') != '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('name') . '%');
            });
        }

        // Example filter by year (if applicable)
        if ($request->has('year') && $request->input('year') != '') {
            // Assuming you have a way to filter by year, adjust as necessary
            // For example, if you have a 'created_at' field, you can filter by year
            $query->whereYear('created_at', $request->input('year'));
        }

        $leaveBalances = $query->paginate(10); // Paginate results

        return view('admin.annual-leave-balance.index', compact('leaveBalances', 'users'));
    }

    // Display the form for creating a new annual leave balance
    public function create(Request $request)
    {
        $user = User::findOrFail($request->input('user_id')); // Get the user based on the ID passed
        return view('admin.annual-leave-balance.create', compact('user'));
    }

    // Store a newly created annual leave balance in storage
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'annual_leave_balance' => 'required|integer|min:0',
        ]);

        AnnualLeaveBalance::create([
            'user_id' => $request->user_id,
            'annual_leave_balance' => $request->annual_leave_balance,
        ]);

        return redirect()->route('admin.annual-leave-balance.index')->with('success', 'Annual leave balance added successfully.');
    }

    // Update the specified annual leave balance in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'annual_leave_balance' => 'required|integer|min:0',
        ]);

        $leaveBalance = AnnualLeaveBalance::findOrFail($id);
        $leaveBalance->update([
            'user_id' => $request->user_id,
            'annual_leave_balance' => $request->annual_leave_balance,
        ]);

        return redirect()->route('admin.annual-leave-balance.index')->with('success', 'Annual leave balance updated successfully.');
    }

    // Display the used leave records for a specific user
    public function showUsedLeave($userId)
    {
        $usedLeaves = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereHas('leaveType', function($query) {
                $query->where('code', 'AL');
            })
            ->get();
        
        // Add this line to fetch the leave balance
        $leaveBalance = AnnualLeaveBalance::where('user_id', $userId)->first();
        
        return view('admin.annual-leave-balance.used-leave', compact('usedLeaves', 'leaveBalance'));
    }

    public function export()
    {
        try {
            $leaveBalances = AnnualLeaveBalance::with('user')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=annual_leave_balances.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
                'Employee ID',
                'Employee Name',
                'Annual Leave Balance',
                'Last Updated',
                'Used Leave Count'
            ]);

            // Write data
            foreach ($leaveBalances as $balance) {
                $usedLeaveCount = Leave::where('user_id', $balance->user_id)
                    ->where('status', 'approved')
                    ->count();

                fputcsv($output, [
                    "=\"{$balance->user_id}\"",
                    $balance->user->username,
                    $balance->annual_leave_balance,
                    Carbon::parse($balance->updated_at)->format('d/m/Y'),
                    $usedLeaveCount
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

    // Add this new method to your existing controller
    public function exportUsedLeave($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $usedLeaves = Leave::with('leaveType')
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=used_leave_history.csv',
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
                'Status',
                'Applied Date'
            ]);

            // Write data
            foreach ($usedLeaves as $leave) {
                fputcsv($output, [
                    $user->username,
                    $leave->leaveType->leave_type,
                    date('d/m/Y', strtotime($leave->from_date)),
                    date('d/m/Y', strtotime($leave->to_date)),
                    $leave->number_of_days,
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

    // Add this new method
    public function deductAnnualLeave($userId, $days)
    {
        $leaveBalance = AnnualLeaveBalance::where('user_id', $userId)->first();
        
        if (!$leaveBalance) {
            return false;
        }

        return $leaveBalance->deductLeaveDays($days);
    }

    public function edit($id)
    {
        $leaveBalance = AnnualLeaveBalance::findOrFail($id);
        $users = User::all();
        
        return view('admin.annual-leave-balance.edit', compact('leaveBalance', 'users'));
    }
}