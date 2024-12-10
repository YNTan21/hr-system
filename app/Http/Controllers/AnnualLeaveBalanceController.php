<?php

namespace App\Http\Controllers;

use App\Models\AnnualLeaveBalance;
use App\Models\User;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // Approve leave request
    public function approveLeave(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1', // Validate the number of days
        ]);

        $leaveBalance = AnnualLeaveBalance::findOrFail($id);

        if ($leaveBalance->deductLeaveDays($request->input('days'))) {
            return redirect()->route('admin.leave.index')->with('success', 'Leave approved and days deducted successfully.');
        } else {
            return redirect()->back()->with('error', 'Not enough leave balance to approve this request.');
        }
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
        $usedLeaves = Leave::where('user_id', $userId)->where('status', 'approved')->get();
        return view('admin.annual-leave-balance.used-leave', compact('usedLeaves'));
    }
}