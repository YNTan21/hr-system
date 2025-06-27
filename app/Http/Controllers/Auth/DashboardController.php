<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Leave;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('is_admin', false)->where('status', 'active')->count();
        $today = Carbon::today();

        // Attendance stats (only for active, non-admin users)
        $activeUserIds = User::where('is_admin', false)->where('status', 'active')->pluck('id');
        $onTime = Attendance::whereDate('date', $today)
            ->where('status', 'on_time')
            ->whereIn('user_id', $activeUserIds)
            ->count();
        $late = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->whereIn('user_id', $activeUserIds)
            ->count();

        // Get users on approved leave today from leaves table
        $onLeaveApprovedUserIds = Leave::where('status', 'approved')
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->whereIn('user_id', $activeUserIds)
            ->pluck('user_id')
            ->unique();
        $leave = $onLeaveApprovedUserIds->count();

        // Employee stats
        $presentEmployeeIds = Attendance::whereDate('date', $today)
            ->where('status', '!=', 'leave')
            ->whereIn('user_id', $activeUserIds)
            ->pluck('user_id')
            ->unique();
        $onLeaveEmployeeIds = $onLeaveApprovedUserIds;
        $absentEmployeeIds = User::where('is_admin', false)
            ->where('status', 'active')
            ->whereNotIn('id', $presentEmployeeIds->merge($onLeaveEmployeeIds))
            ->pluck('id');

        $presentCount = $presentEmployeeIds->count();
        $onLeaveCount = $onLeaveEmployeeIds->count();
        $absentCount = $absentEmployeeIds->count();

        // Get user details for each group
        $presentEmployees = User::whereIn('id', $presentEmployeeIds)->get();
        $onLeaveEmployees = User::whereIn('id', $onLeaveEmployeeIds)->get();
        $absentEmployees = User::whereIn('id', $absentEmployeeIds)->get();

        // Leave stats (today, only for active users)
        $pendingLeaves = Leave::whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where('status', 'pending')
            ->whereIn('user_id', $activeUserIds)
            ->with('user')
            ->get();
        $approvedLeaves = Leave::whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where('status', 'approved')
            ->whereIn('user_id', $activeUserIds)
            ->get();
        $rejectedLeaves = Leave::whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where('status', 'rejected')
            ->whereIn('user_id', $activeUserIds)
            ->get();

        // Overtime data (only for active, non-admin users)
        $users = User::where('is_admin', false)->where('status', 'active')->get();
        $usernames = $users->pluck('username')->toArray();
        $overtimeHours = [];
        foreach ($users as $user) {
            $overtime = Attendance::where('user_id', $user->id)
                ->whereMonth('date', now()->month)
                ->whereNotNull('overtime')
                ->sum('overtime');
            $overtimeHours[] = $overtime;
        }

        return view('admin.dashboard', compact(
            'totalEmployees', 'onTime', 'late', 'leave',
            'usernames', 'overtimeHours',
            'presentCount', 'absentCount', 'onLeaveCount',
            'presentEmployees', 'absentEmployees', 'onLeaveEmployees',
            'pendingLeaves', 'approvedLeaves', 'rejectedLeaves'
        ));
    }

    public function getOvertimeData($month)
    {
        try {
            DB::enableQueryLog();
            
            $year = date('Y');
            $startDate = Carbon::create($year, $month, 1, 0, 0, 0);
            $endDate = $startDate->copy()->endOfMonth();
            
            \Log::info('Query Parameters:', [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'month' => $month,
                'year' => $year
            ]);

            $overtimeData = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->whereNotNull('attendances.overtime')
                ->where('attendances.overtime', '!=', 0)
                ->select(
                    'users.username as name',
                    DB::raw('SUM(attendances.overtime) as total_hours')
                )
                ->groupBy('users.username')  // 只按用户名分组
                ->get();

            \Log::info('Query Result:', [
                'data' => $overtimeData,
                'count' => $overtimeData->count(),
                'sql' => DB::getQueryLog()
            ]);

            return response()->json([
                'usernames' => $overtimeData->pluck('name'),
                'overtimeHours' => $overtimeData->pluck('total_hours'),
                'debug' => [
                    'month' => $month,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'count' => $overtimeData->count(),
                    'data' => $overtimeData
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Overtime data error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
