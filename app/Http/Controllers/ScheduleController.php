<?php

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
            : Carbon::now()->startOfDay();

        $currentWeekEnd = $currentWeekStart->copy()->addDays(6);

        $employees = User::all();
        $schedules = Schedule::whereBetween('shift_date', [
            $currentWeekStart->format('Y-m-d'),
            $currentWeekEnd->format('Y-m-d')
        ])->get();

        \Log::info('Schedule Query', [
            'week_start' => $currentWeekStart->format('Y-m-d'),
            'week_end' => $currentWeekEnd->format('Y-m-d'),
            'schedules_count' => $schedules->count(),
            'schedules' => $schedules->toArray()
        ]);

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
                return redirect()->route('admin.schedule.index')->with('warning', $message);
            }
            return redirect()->route('admin.schedule.index')->with('success', $message);
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
        // Get the start date for the selected week or use the current date if not provided
        $selectedDate = $request->get('week_start') 
            ? Carbon::parse($request->get('week_start')) 
            : now();

        // Get the start of the week for the selected date
        $currentWeekStart = $selectedDate->startOfWeek();
        $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

        // Fetch employees (if needed for other logic)
        $employees = User::all();

        // Fetch schedules for the selected week
        $schedules = Schedule::query()
            ->whereBetween('shift_date', [
                $currentWeekStart->format('Y-m-d'),
                $currentWeekEnd->format('Y-m-d')
            ])
            ->with('user')  // Assuming you want to include user data
            ->get();

        // Log information for debugging
        \Log::info('Week start: ' . $currentWeekStart->format('Y-m-d'));
        \Log::info('Date Range: ' . $currentWeekStart->format('Y-m-d') . ' to ' . $currentWeekEnd->format('Y-m-d'));

        // Return the view with schedules and employees data
        return view('admin.schedule.timesheet', compact('employees', 'schedules', 'currentWeekStart', 'currentWeekEnd'));
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
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

        // Fetch schedules for the current week
        $schedules = Schedule::query()
            ->whereBetween('shift_date', [$currentWeekStart->format('Y-m-d'), $currentWeekEnd->format('Y-m-d')])
            ->with('user')
            ->get();

        return view('admin.schedule.current', [
            'schedules' => $schedules,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd
        ]);
    }


    
}
