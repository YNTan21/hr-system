<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $currentMonth = request('month', now()->month);
        $currentYear = request('year', now()->year);
        
        // Get leaves with relationships
        $leaves = Leave::with(['user', 'leaveType'])
            ->where(function($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('from_date', $currentMonth)
                      ->whereYear('from_date', $currentYear)
                      ->orWhere(function($q) use ($currentMonth, $currentYear) {
                          $q->whereMonth('to_date', $currentMonth)
                            ->whereYear('to_date', $currentYear);
                      });
            })
            ->get();

        // Get schedules with correct column name
        $schedules = Schedule::with('user')
            ->whereMonth('shift_date', $currentMonth)
            ->whereYear('shift_date', $currentYear)
            ->get();

        $calendar = $this->generateCalendar($currentMonth, $currentYear, $leaves, $schedules);
        $leaveTypes = LeaveType::all();
        
        $monthName = Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F');
        
        // Get months for dropdown
        $months = collect(range(1, 12))->map(function($month) {
            return [
                'value' => $month,
                'name' => date('F', mktime(0, 0, 0, $month, 1))
            ];
        });

        // Get years for dropdown (current year ± 2 years)
        $years = range(now()->year - 2, now()->year + 2);

        // Calculate previous and next month/year
        $prevMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
        $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;

        return view('admin.calendar.index', compact(
            'calendar',
            'currentMonth',
            'currentYear',
            'monthName',
            'months',
            'years',
            'leaveTypes',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear'
        ));
    }

    private function generateCalendar($month, $year, $leaves, $schedules)
    {
        $calendar = [];
        
        $firstDay = Carbon::createFromDate($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $currentDay = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);
        
        while ($currentDay <= $lastDay->copy()->endOfWeek(Carbon::SATURDAY)) {
            $dayKey = $currentDay->format('Y-m-d');
            
            $calendar[$dayKey] = [
                'day' => $currentDay->day,
                'isCurrentMonth' => $currentDay->month === $month,
                'isToday' => $currentDay->isToday(),
                'leaves' => [],
                'schedules' => []
            ];
            
            // Add leaves for this day
            foreach ($leaves as $leave) {
                $fromDate = Carbon::parse($leave->from_date);
                $toDate = Carbon::parse($leave->to_date);
                
                if ($currentDay->between($fromDate, $toDate)) {
                    $calendar[$dayKey]['leaves'][] = $leave;
                }
            }

            // Add schedules for this day - using shift_date
            foreach ($schedules as $schedule) {
                $scheduleDate = Carbon::parse($schedule->shift_date);
                
                if ($currentDay->isSameDay($scheduleDate)) {
                    $calendar[$dayKey]['schedules'][] = $schedule;
                }
            }
            
            $currentDay->addDay();
        }
        
        return $calendar;
    }
}