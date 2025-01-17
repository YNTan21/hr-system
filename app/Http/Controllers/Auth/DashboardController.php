<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 统计员工总数
        $totalEmployees = User::where('is_admin', false)->count();
        
        $today = Carbon::today();
        
        // 统计今天的考勤状态
        $onTime = Attendance::whereDate('date', $today)
            ->where('status', 'on_time')
            ->count();
            
        $late = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();
            
        $leave = Attendance::whereDate('date', $today)
            ->where('status', 'leave')
            ->count();

        // 获取加班数据
        $users = User::where('is_admin', false)->get();  // 只获取非管理员用户
        $usernames = $users->pluck('username')->toArray();
        $overtimeHours = [];

        foreach ($users as $user) {
            // 从 Attendance 表计算加班时间
            $overtime = Attendance::where('user_id', $user->id)
                ->whereMonth('date', now()->month)
                ->whereNotNull('overtime')
                ->sum('overtime');
            
            $overtimeHours[] = $overtime;
        }

        return view('admin.dashboard', compact(
            'totalEmployees', 
            'onTime', 
            'late',
            'leave',
            'usernames',
            'overtimeHours'
        ));
    }

    public function getOvertimeData($month)
    {
        try {
            $year = date('Y');
            $startDate = Carbon::create($year, $month, 1, 0, 0, 0);
            $endDate = $startDate->copy()->endOfMonth();
            
            $overtimeData = DB::table('attendances')
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.overtime_hours', '>', 0)
                ->select(
                    'users.name',
                    DB::raw('SUM(CASE WHEN overtime_hours > 0 THEN overtime_hours ELSE 0 END) as total_hours')
                )
                ->groupBy('users.name')
                ->get();

            return response()->json([
                'usernames' => $overtimeData->pluck('name'),
                'overtimeHours' => $overtimeData->pluck('total_hours'),
                'debug' => [
                    'month' => $month,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'count' => $overtimeData->count()
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
