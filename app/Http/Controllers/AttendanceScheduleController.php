<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSchedule;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceScheduleController extends Controller
{
    // 显示当天的所有 Schedule 和 Attendance
public function index(Request $request)
{
    $date = $request->input('filter_date', now()->toDateString());

    // Fetch schedules
    $schedules = Schedule::with(['user', 'attendanceSchedules' => function($query) use ($date) {
        $query->where('date', $date);
    }])
    ->where('shift_date', $date)
    ->get();

    // Calculate status and overtime for each attendance
    foreach ($schedules as $schedule) {
        $attendance = $schedule->attendanceSchedules->first(); // Assuming one attendance per schedule

        if ($attendance) {
            // Calculate status if clock_in exists
            if ($attendance->clock_in) {
                $attendance->status = $attendance->calculateStatus();
            }

            // Calculate overtime if clock_out exists
            if ($attendance->clock_out) {
                $attendance->overtime_hour = $attendance->calculateOvertime();
                // Update status if overtime is more than 0.5 hours
                if ($attendance->overtime_hour > 0.5) {
                    $attendance->status = 'overtime';
                }
            }

            // Save the updated attendance
            $attendance->save();
        }
    }

    return view('admin.attendance-schedule.index', compact('schedules', 'date'));
}

    

    public function create()
    {
        $users = User::all(); // 获取所有用户
        $schedules = Schedule::whereDate('shift_date', Carbon::today())->get();

        return view('admin.attendance-schedule.create', compact('users', 'schedules'));
    }

    // 添加或更新 Attendance
    public function store(Request $request)
    {
        // Log the incoming data for debugging
        \Log::info('Attendance store called with data:', $request->all());

        // Validate the form data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'clock_in' => 'nullable|date_format:Y-m-d\TH:i',
            'clock_out' => 'nullable|date_format:Y-m-d\TH:i|after:clock_in',
            'date' => 'required|date'
        ]);

        try {
            // Find existing attendance or create new one
            $attendance = AttendanceSchedule::where([
                'schedules_id' => $validated['schedule_id'],
                'user_id' => $validated['user_id'],
                'date' => $validated['date']
            ])->first();

            if ($attendance) {
                // Update existing record
                $attendance->update([
                    'clock_in' => $validated['clock_in'],
                    'clock_out' => $validated['clock_out'],
                    'status' => $this->calculateStatus($validated['clock_in'], $validated['clock_out'], $validated['date'])
                ]);
            } else {
                // Create new record
                $attendance = AttendanceSchedule::create([
                    'schedules_id' => $validated['schedule_id'],
                    'user_id' => $validated['user_id'],
                    'date' => $validated['date'],
                    'clock_in' => $validated['clock_in'],
                    'clock_out' => $validated['clock_out'],
                    'status' => $this->calculateStatus($validated['clock_in'], $validated['clock_out'], $validated['date'])
                ]);
            }

            return redirect()->route('admin.attendance-schedule.index', ['filter_date' => $validated['date']])
                ->with('success', 'Attendance record ' . ($attendance->wasRecentlyCreated ? 'created' : 'updated') . ' successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to save attendance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to save attendance record: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function calculateStatus($clockIn, $clockOut, $scheduleStartTime)
    {
        if (!$clockIn) {
            return 'absent';
        }
    
        $clockInTime = Carbon::parse($clockIn);
        $scheduleStart = Carbon::parse($scheduleStartTime); // Ensure this is valid
    
        if ($clockInTime->gt($scheduleStart)) {
            return 'late';
        }
    
        if ($clockOut) {
            $clockOutTime = Carbon::parse($clockOut);
            $scheduledEnd = $scheduleStart->copy()->addHours(8); // Replace 8 with dynamic scheduled duration
    
            if ($clockOutTime->gt($scheduledEnd)) {
                return 'overtime';
            }
        }
    
        return 'on time';
    }
    

    private function calculateOvertime($clockIn, $clockOut, $scheduledHours = null)
    {
        if (!$clockIn || !$clockOut) {
            return 0;
        }
    
        $clockInTime = Carbon::parse($clockIn);
        $clockOutTime = Carbon::parse($clockOut);
    
        // Dynamically fetch scheduled hours if not passed
        if (!$scheduledHours) {
            $schedule = Schedule::find($this->schedule_id); // Adjust to your model logic
            $scheduledHours = $schedule ? $schedule->duration_hours : 8; // Replace 8 with default
        }
    
        $totalWorkedMinutes = $clockOutTime->diffInMinutes($clockInTime);
        $totalWorkedHours = $totalWorkedMinutes / 60;
    
        return max(0, $totalWorkedHours - $scheduledHours);
    }
}