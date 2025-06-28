<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Leave;
use App\Models\KpiEntry;
use App\Models\KPIGoal;
use App\Models\Position;
use App\Models\AnnualLeaveBalance;
use App\Models\LeaveType;

class EnhancedDashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('is_admin', false)->where('status', 'active')->count();
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

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

        // Additional Dashboard Data
        $kpiStats = $this->getKPIStats($currentMonth, $currentYear);
        $leaveTrends = $this->getLeaveTrends();
        $positionStats = $this->getPositionStats();
        $leaveBalanceOverview = $this->getLeaveBalanceOverview();
        $attendancePatterns = $this->getAttendancePatterns($currentMonth, $currentYear);
        $recentActivities = $this->getRecentActivities();

        return view('admin.enhanced-dashboard', compact(
            'totalEmployees', 'onTime', 'late', 'leave',
            'usernames', 'overtimeHours',
            'presentCount', 'absentCount', 'onLeaveCount',
            'presentEmployees', 'absentEmployees', 'onLeaveEmployees',
            'pendingLeaves', 'approvedLeaves', 'rejectedLeaves',
            'kpiStats', 'leaveTrends', 'positionStats', 'leaveBalanceOverview',
            'attendancePatterns', 'recentActivities'
        ));
    }

    private function getKPIStats($month, $year)
    {
        $totalKPIEntries = KpiEntry::where('month', $month)
            ->where('year', $year)
            ->count();
            
        $completedKPIEntries = KpiEntry::where('month', $month)
            ->where('year', $year)
            ->where('status', 'completed')
            ->count();
            
        $pendingKPIEntries = KpiEntry::where('month', $month)
            ->where('year', $year)
            ->where('status', 'pending')
            ->count();
            
        $averageScore = KpiEntry::where('month', $month)
            ->where('year', $year)
            ->avg('final_score') ?? 0;

        return [
            'total' => $totalKPIEntries,
            'completed' => $completedKPIEntries,
            'pending' => $pendingKPIEntries,
            'average_score' => round($averageScore, 2),
            'completion_rate' => $totalKPIEntries > 0 ? round(($completedKPIEntries / $totalKPIEntries) * 100, 1) : 0
        ];
    }

    private function getLeaveTrends()
    {
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $approvedLeaves = Leave::where('status', 'approved')
                ->whereMonth('from_date', $month)
                ->whereYear('from_date', $year)
                ->count();
                
            $pendingLeaves = Leave::where('status', 'pending')
                ->whereMonth('from_date', $month)
                ->whereYear('from_date', $year)
                ->count();
                
            $trends[] = [
                'month' => $date->format('M Y'),
                'approved' => $approvedLeaves,
                'pending' => $pendingLeaves,
                'total' => $approvedLeaves + $pendingLeaves
            ];
        }
        
        return $trends;
    }

    private function getPositionStats()
    {
        $positions = Position::where('status', 'active')->get();
        $stats = [];
        
        foreach ($positions as $position) {
            $employeeCount = User::where('position_id', $position->id)
                ->where('is_admin', false)
                ->where('status', 'active')
                ->count();
                
            $presentToday = Attendance::whereDate('date', Carbon::today())
                ->whereHas('user', function($query) use ($position) {
                    $query->where('position_id', $position->id);
                })
                ->count();
                
            $stats[] = [
                'position_name' => $position->position_name,
                'employee_count' => $employeeCount,
                'present_today' => $presentToday,
                'attendance_rate' => $employeeCount > 0 ? round(($presentToday / $employeeCount) * 100, 1) : 0
            ];
        }
        
        return $stats;
    }

    private function getLeaveBalanceOverview()
    {
        $users = User::where('is_admin', false)
            ->where('status', 'active')
            ->with('annualLeaveBalance')
            ->get();
            
        $totalBalance = 0;
        $lowBalanceUsers = [];
        
        foreach ($users as $user) {
            $balance = $user->annualLeaveBalance->annual_leave_balance ?? 0;
            $totalBalance += $balance;
            
            if ($balance <= 5) {
                $lowBalanceUsers[] = [
                    'username' => $user->username,
                    'balance' => $balance
                ];
            }
        }
        
        return [
            'total_balance' => $totalBalance,
            'average_balance' => $users->count() > 0 ? round($totalBalance / $users->count(), 1) : 0,
            'low_balance_users' => $lowBalanceUsers,
            'total_users' => $users->count()
        ];
    }

    private function getAttendancePatterns($month, $year)
    {
        $totalDays = Carbon::create($year, $month, 1)->daysInMonth;
        $workingDays = 0;
        
        for ($day = 1; $day <= $totalDays; $day++) {
            $date = Carbon::create($year, $month, $day);
            if ($date->isWeekday()) {
                $workingDays++;
            }
        }
        
        $totalAttendance = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->count();
            
        $onTimeAttendance = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'on_time')
            ->count();
            
        $lateAttendance = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'late')
            ->count();
            
        $totalOvertime = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('overtime')
            ->sum('overtime');

        return [
            'working_days' => $workingDays,
            'total_attendance' => $totalAttendance,
            'on_time_count' => $onTimeAttendance,
            'late_count' => $lateAttendance,
            'total_overtime' => round($totalOvertime, 2),
            'punctuality_rate' => $totalAttendance > 0 ? round(($onTimeAttendance / $totalAttendance) * 100, 1) : 0
        ];
    }

    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent leave requests
        $recentLeaves = Leave::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($recentLeaves as $leave) {
            $activities[] = [
                'type' => 'leave_request',
                'user' => $leave->user->username,
                'action' => 'submitted leave request',
                'details' => $leave->leaveType->leave_type . ' from ' . $leave->from_date->format('M d') . ' to ' . $leave->to_date->format('M d'),
                'time' => $leave->created_at->diffForHumans(),
                'status' => $leave->status
            ];
        }
        
        // Recent KPI entries
        $recentKPIs = KpiEntry::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($recentKPIs as $kpi) {
            $activities[] = [
                'type' => 'kpi_entry',
                'user' => $kpi->user->username,
                'action' => 'submitted KPI entry',
                'details' => 'Score: ' . $kpi->final_score,
                'time' => $kpi->created_at->diffForHumans(),
                'status' => $kpi->status
            ];
        }
        
        // Sort by creation time and return top 10
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 10);
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
                ->groupBy('users.username')
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
