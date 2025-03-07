s<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $currentWeekStart = $request->query('week_start') 
            ? Carbon::parse($request->query('week_start'))
            : Carbon::now()->startOfWeek(Carbon::SUNDAY);

        $currentWeekStart->setTimezone('Asia/Kuala_Lumpur');
        $currentWeekEnd = $currentWeekStart->copy()->addDays(6);

        $employees = User::all();
        $schedules = Schedule::whereBetween('shift_date', [
            $currentWeekStart->format('Y-m-d'),
            $currentWeekEnd->format('Y-m-d')
        ])->get();

        return view('admin.schedule.index', compact('employees', 'schedules', 'currentWeekStart'));
    }

    public function create()
    {
        $employees = User::all();
        return view('admin.schedule.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'shift_date' => 'required|date',
            'shift_code' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // 设置马来西亚时区
        date_default_timezone_set('Asia/Kuala_Lumpur');
        
        $created = 0;
        $errors = [];

        foreach ($request->user_ids as $userId) {
            try {
                Schedule::create([
                    'user_id' => $userId,
                    'shift_date' => $request->shift_date,
                    'shift_code' => $request->shift_code,
                    'start_time' => Carbon::parse($request->start_time, 'Asia/Kuala_Lumpur')->format('H:i:s'),
                    'end_time' => Carbon::parse($request->end_time, 'Asia/Kuala_Lumpur')->format('H:i:s'),
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Failed to create schedule for user ID: $userId";
            }
        }

        if ($created > 0) {
            $message = "Successfully created $created schedule(s)";
            if (count($errors) > 0) {
                $message .= ", but encountered some errors: " . implode(', ', $errors);
                return redirect()->route('admin.schedule.index', [
                    'week_start' => Carbon::parse($request->shift_date)->startOfWeek()->format('Y-m-d')
                ])->with('warning', $message);
            }
            return redirect()->route('admin.schedule.index', [
                'week_start' => Carbon::parse($request->shift_date)->startOfWeek()->format('Y-m-d')
            ])->with('success', $message);
        }

        return redirect()->route('admin.schedule.create')
            ->with('error', 'Failed to create schedules: ' . implode(', ', $errors))
            ->withInput();
    }

    public function edit(Schedule $schedule)
    {
        $employees = User::all();

        return view('admin.schedule.edit', compact('schedule', 'employees'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedule.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedule.index')->with('success', 'Schedule deleted successfully.');
    }

    public function timesheet(Request $request)
    {
        // If a week is selected, parse it correctly
        if ($request->filled('week')) {
            // Convert the HTML week input format (e.g., "2024-W10") to a date
            $yearWeek = explode('-W', $request->week);
            $year = $yearWeek[0];
            $week = $yearWeek[1];
            $date = Carbon::now()->setISODate($year, $week);
        } else {
            $date = Carbon::now();
        }

        // Get the start and end of the week
        $currentWeekStart = $date->copy()->startOfWeek();
        $currentWeekEnd = $date->copy()->endOfWeek();

        $schedules = Schedule::whereBetween('shift_date', [
            $currentWeekStart->format('Y-m-d'),
            $currentWeekEnd->format('Y-m-d')
        ])
        ->with('user')
        ->get();

        return view('admin.schedule.timesheet', compact('schedules', 'currentWeekStart', 'currentWeekEnd'));
    }


    public function select()
    {
        return view('admin.schedule.select');
    }

    public function view(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $week = $request->input('week');

        // Calculate the start and end dates of the selected week
        $currentWeekStart = Carbon::createFromDate($year, $month, 1)->startOfMonth()->addWeeks($week - 1)->startOfWeek();
        $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

        // Fetch schedules for the selected week
        $schedules = Schedule::query()
            ->whereBetween('shift_date', [$currentWeekStart->format('Y-m-d'), $currentWeekEnd->format('Y-m-d')])
            ->with('user')
            ->get();

        return view('admin.schedule.timesheet', [
            'schedules' => $schedules,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd
        ]);
    }

    public function currentWeek()
    {
        // 明确设置时区为吉隆坡时间
        $currentWeekStart = Carbon::now('Asia/Kuala_Lumpur')->startOfWeek();
        $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

        // 获取本周的所有排班记录
        $schedules = Schedule::query()
            ->whereBetween('shift_date', [
                $currentWeekStart->format('Y-m-d'),
                $currentWeekEnd->format('Y-m-d')
            ])
            ->with('user')
            ->get();

        return view('admin.schedule.current', [
            'schedules' => $schedules,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd
        ]);
    }

    public function calendar(Request $request)
    {
        // Get current date for comparison
        $currentDate = Carbon::now();
        
        // Get requested month and year, default to current month if not specified
        $month = $request->query('month', $currentDate->month);
        $year = $request->query('year', $currentDate->year);
        
        // Create date from request parameters
        $requestedDate = Carbon::create($year, $month, 1);
        
        // Get the first day of the month
        $firstDay = Carbon::create($year, $month, 1);
        
        // Get the start and end dates for the calendar view
        $start = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);
        $end = $firstDay->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        
        // Get all users
        $users = User::orderBy('username')->get();
        
        // Get all schedules for the visible calendar period
        $schedules = Schedule::whereBetween('shift_date', [
                $start->format('Y-m-d'), 
                $end->format('Y-m-d')
            ])
            ->with(['user'])
            ->get();
        
        $calendar = [];
        $currentDate = $start->copy();
        
        // Generate calendar data
        while ($currentDate <= $end) {
            $date = $currentDate->format('Y-m-d');
            
            // Get schedules for this day
            $daySchedules = $schedules->filter(function($schedule) use ($date) {
                if (!$schedule->shift_date) return false;
                return $schedule->shift_date->format('Y-m-d') === $date;
            });
            
            $calendar[] = [
                'day' => $currentDate->format('j'),
                'date' => $date,
                'isCurrentMonth' => $currentDate->month == $month,
                'isToday' => $currentDate->isToday(),
                'schedules' => $daySchedules
            ];
            
            $currentDate->addDay();
        }
        
        return view('admin.schedule.calendar', compact('calendar', 'users'));
    }
}
