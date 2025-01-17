<?php

namespace App\Http\Controllers;

use DateTime;
// use App\User;
use App\Latetime;
// use App\Attendance;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AttendanceEmp;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Models\Schedule;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendance.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user')->select('attendances.*');

        // 用户筛选
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 日期筛选
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // 月份筛选
        if ($request->filled('month')) {
            $month = $request->month;
            $query->whereYear('date', substr($month, 0, 4))
                  ->whereMonth('date', substr($month, 5, 2));
        }

        // 周筛选
        if ($request->filled('week')) {
            $week = $request->week;
            $year = substr($week, 0, 4);
            $weekNumber = substr($week, 6);
            
            $startDate = Carbon::now()
                ->setISODate($year, $weekNumber)
                ->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();

            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // 改用 paginate 而不是 get
        $attendances = $query->orderBy('date', 'desc')->paginate(10);
        $users = User::all();

        return view('admin.attendance.index', compact('attendances', 'users'));
    }

    /**
     * Display a listing of the latetime.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexLatetime()
    {
        return view('admin.latetime')->with(['latetimes' => Latetime::all()]);
    }



    /**
     * assign attendance to employee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign(AttendanceEmp $request)
    {
        $request->validated();

        if ($employee = User::whereEmail(request('email'))->first()){

            if (Hash::check($request->pin_code, $employee->pin_code)) {
                    if (!Attendance::whereAttendance_date(date("Y-m-d"))->whereUser_id($employee->id)->first()){
                        $attendance = new Attendance;
                        $attendance->user_id = $employee->id;
                        $attendance->attendance_time = date("H:i:s");
                        $attendance->attendance_date = date("Y-m-d");

                        if (!($employee->schedules->first()->time_in >= $attendance->attendance_time)){
                            $attendance->status = 0;
                        AttendanceController::lateTime($employee);
                        };
                        $attendance->save();

                    }else{
                        return redirect()->route('attendance.login')->with('error', 'you assigned your attendance before');
                    }
                } else {
                return redirect()->route('attendance.login')->with('error', 'Failed to assign the attendance');
            }
        }



        return redirect()->route('home')->with('success', 'Successful in assign the attendance');
    }

    /**
     * assign late time for attendace .
     *
     * @return \Illuminate\Http\Response
     */
    public static function lateTime(User $employee)
    {
        $current_t= new DateTime(date("H:i:s"));
        $start_t= new DateTime($employee->schedules->first()->time_in);
        $difference = $start_t->diff($current_t)->format('%H:%I:%S');


        $latetime = new Latetime;
        $latetime->user_id = $employee->id;
        $latetime->duration = $difference;
        $latetime->latetime_date = date("Y-m-d");
        $latetime->save();

    }

    public function create()
    {
        $users = User::where('status', 'active')
            ->orderBy('username')
            ->get();
        
        return view('admin.attendance.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in_time' => 'required',
            'clock_out_time' => 'nullable',
            'status' => 'required|in:on_time,late'
        ]);

        try {
            date_default_timezone_set('Asia/Kuala_Lumpur');
            
            $clockInTime = $validated['clock_in_time'] ? 
                Carbon::parse($validated['date'] . ' ' . $validated['clock_in_time'])->format('Y-m-d H:i:s') : 
                null;
                
            $clockOutTime = $validated['clock_out_time'] ? 
                Carbon::parse($validated['date'] . ' ' . $validated['clock_out_time'])->format('Y-m-d H:i:s') : 
                null;

            $schedule = Schedule::where('user_id', $validated['user_id'])
                ->whereDate('shift_date', $validated['date'])
                ->first();

            $overtime = '00:00';

            if ($schedule && $clockOutTime) {
                $scheduleEndTime = Carbon::parse($validated['date'] . ' ' . $schedule->end_time);
                $actualEndTime = Carbon::parse($clockOutTime);

                // 计算时间差（不考虑正负）
                $diffInMinutes = abs($actualEndTime->diffInMinutes($scheduleEndTime));
                
                if ($diffInMinutes > 0) {
                    $hours = floor($diffInMinutes / 60);
                    $minutes = $diffInMinutes % 60;
                    $overtime = sprintf('%02d:%02d', $hours, $minutes);
                    
                    \Log::info('Overtime Debug', [
                        'schedule_end' => $scheduleEndTime->format('Y-m-d H:i:s'),
                        'actual_end' => $actualEndTime->format('Y-m-d H:i:s'),
                        'diff_minutes' => $diffInMinutes,
                        'hours' => $hours,
                        'minutes' => $minutes,
                        'overtime' => $overtime
                    ]);
                }
            }

            $attendance = Attendance::create([
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
                'clock_in_time' => $clockInTime,
                'clock_out_time' => $clockOutTime,
                'status' => $validated['status'],
                'overtime' => $overtime
            ]);

            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance recorded successfully');
        } catch (\Exception $e) {
            \Log::error('Attendance Error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to save attendance. ' . $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Attendance::with('user');
            
            if ($request->from_date) {
                $query->whereDate('date', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->whereDate('date', '<=', $request->to_date);
            }
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $attendances = $query->orderBy('date', 'desc')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=attendance_report.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // 写入表头
            fputcsv($output, [
                'Date',
                'Employee ID',
                'Name',
                'Status',
                'Clock In',
                'Clock Out',
                'Overtime'
            ]);

            // 写入数据，为所有日期时间添加引号
            foreach ($attendances as $attendance) {
                $date = Carbon::parse($attendance->date)->format('d/m/Y');  // 改用斜线格式
                $clockIn = $attendance->clock_in_time ? 
                    Carbon::parse($attendance->clock_in_time)->format('H:i:s') : '';
                $clockOut = $attendance->clock_out_time ? 
                    Carbon::parse($attendance->clock_out_time)->format('H:i:s') : '';

                fputcsv($output, [
                    "=\"$date\"",  // 添加引号和等号
                    $attendance->user->id,
                    $attendance->user->username,
                    $attendance->status,
                    "=\"$clockIn\"",  // 添加引号和等号
                    "=\"$clockOut\"",  // 添加引号和等号
                    $attendance->overtime
                ]);
            }

            fclose($output);

            return response()->stream(
                function() {
                    // 数据已经写入输出流
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

    public function edit(Attendance $attendance)
    {
        // 获取所有用户供下拉选择
        $users = User::all();
        
        // 传递当前考勤记录和用户列表到视图
        return view('admin.attendance.edit', [
            'attendance' => $attendance,
            'users' => $users
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        try {
            $attendance->delete();
            return redirect()
                ->route('admin.attendance.index')
                ->with('success', 'Attendance record deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.attendance.index')
                ->with('error', 'Error deleting attendance record');
        }
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in_time' => 'nullable',
            'clock_out_time' => 'nullable',
            'status' => 'required|in:on_time,late'
        ]);

        try {
            // 获取用户当天的排班
            $schedule = Schedule::where('user_id', $validated['user_id'])
                ->whereDate('shift_date', $validated['date'])
                ->first();

            if ($schedule && $validated['clock_out_time']) {
                // 转换时间为 Carbon 实例以便计算
                $scheduleEndTime = Carbon::parse($validated['date'] . ' ' . $schedule->end_time);
                $clockOutTime = Carbon::parse($validated['date'] . ' ' . $validated['clock_out_time']);

                // 计算加班时间（分钟）
                $overtimeMinutes = 0;
                if ($clockOutTime->gt($scheduleEndTime)) {
                    $overtimeMinutes = $clockOutTime->diffInMinutes($scheduleEndTime);
                }

                // 转换分钟为小时和分钟格式 (例如: 2:30)
                $overtimeHours = floor($overtimeMinutes / 60);
                $remainingMinutes = $overtimeMinutes % 60;
                $overtime = sprintf('%02d:%02d', $overtimeHours, $remainingMinutes);
            } else {
                $overtime = '00:00';
            }

            // 更新考勤记录
            $attendance->update([
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
                'clock_in_time' => $validated['clock_in_time'],
                'clock_out_time' => $validated['clock_out_time'],
                'status' => $validated['status'],
                'overtime' => $overtime  // 保存加班时间
            ]);

            return redirect()->route('admin.attendance.index')
                ->with('success', 'Attendance updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update attendance. ' . $e->getMessage()]);
        }
    }

}