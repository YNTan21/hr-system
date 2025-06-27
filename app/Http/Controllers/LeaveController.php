<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;
use App\Models\AnnualLeaveBalance;
use App\Http\Controllers\AnnualLeaveBalanceController;
use Carbon\Carbon;
use App\Models\Position;
use App\Models\Goal;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeaveImport;
use App\Models\LeavePrediction;
use Illuminate\Support\Facades\Http;

class LeaveController extends Controller
{
    use AuthorizesRequests;
    
    public function create()
    {
        $users = User::all();
        $leaveTypes = LeaveType::where('status', 'active')->get();
        return view('admin.leave.create', compact('users','leaveTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id', 
            'leave_type_id' => 'required|exists:leave_type,id', 
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        // Check annual leave balance
        $annualLeaveBalance = AnnualLeaveBalance::where('user_id', $validatedData['user_id'])->first();
        if ($annualLeaveBalance && $annualLeaveBalance->annual_leave_balance < $validatedData['number_of_days']) {
            return redirect()->back()->with('error', 'Not enough annual leave balance to apply for this leave.');
        }

        // Create the leave request
        $leave = Leave::create([
            'user_id' => $validatedData['user_id'],
            'leave_type_id' => $validatedData['leave_type_id'],
            'from_date' => $validatedData['from_date'],
            'to_date' => $validatedData['to_date'],
            'number_of_days' => $validatedData['number_of_days'],
            'reason' => $validatedData['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave created successfully and is pending approval.');
    }

    public function index(Request $request)
    {
        $query = Leave::with(['user', 'leaveType']);

        // User Filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Year Filter
        if ($request->filled('year')) {
            $query->whereYear('from_date', $request->year);
        }

        // Month Filter
        if ($request->filled('month')) {
            $query->whereMonth('from_date', $request->month);
        }

        // Leave Type Filter
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->filled('filter_date')) {
            $query->whereDate('from_date', $request->filter_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get other data for dropdowns
        $users = User::all();
        $leaveTypes = LeaveType::all();

        $predictions = LeavePrediction::where('date', '>=', now())
            ->where('date', '<=', now()->addDays(30))
            ->orderBy('date')
            ->get();

        return view('admin.leave.index', compact('leaves', 'users', 'leaveTypes', 'predictions'));
    }

    public function edit($id)
    {
        $leave = Leave::with(['user', 'leaveType'])->findOrFail($id);
        $users = User::all();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('admin.leave.edit', compact('leave', 'users', 'leaveTypes'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_type,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $leave = Leave::findOrFail($id);
        
        // Check if this is an annual leave and the status is being changed to approved
        if ($leave->leaveType->code === 'AL' && 
            $validatedData['status'] === 'approved' && 
            $leave->status !== 'approved') {
            
            // Check annual leave balance
            $annualLeaveBalance = AnnualLeaveBalance::where('user_id', $validatedData['user_id'])->first();
            if ($annualLeaveBalance && $annualLeaveBalance->annual_leave_balance < $validatedData['number_of_days']) {
                return redirect()->back()->with('error', 'Not enough annual leave balance to approve this leave.');
            }
            
            // If approved, deduct from balance
            $annualLeaveController = new AnnualLeaveBalanceController();
            $deductionSuccess = $annualLeaveController->deductAnnualLeave(
                $validatedData['user_id'], 
                $validatedData['number_of_days']
            );
            
            if (!$deductionSuccess) {
                return redirect()->back()->with('error', 'Failed to update annual leave balance.');
            }
        }

        $leave->update($validatedData);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        
        // If it's an approved annual leave, restore the balance
        if ($leave->status === 'approved' && $leave->leaveType->code === 'AL') {
            $annualLeaveBalance = AnnualLeaveBalance::where('user_id', $leave->user_id)->first();
            if ($annualLeaveBalance) {
                $annualLeaveBalance->annual_leave_balance += $leave->number_of_days;
                $annualLeaveBalance->save();
            }
        }
        
        $leave->delete();

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave request deleted successfully.');
    }

    public function processLeaves(Request $request)
    {
        $query = Leave::with(['user', 'leaveType'])->where('status', 'pending');

        // Apply filters if they are present in the request
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('filter_date')) {
            $filterDate = $request->filter_date;
            $query->where(function($q) use ($filterDate) {
                $q->where('from_date', '<=', $filterDate)
                  ->where('to_date', '>=', $filterDate);
            });
        }

        // Order by created_at in descending order to show newest leaves first
        $query->orderBy('created_at', 'desc');

        // Paginate the results
        $leaves = $query->paginate(10);

        // Get only active leave types for the filters
        $users = User::all();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('admin.leave.process', compact('leaves', 'users', 'leaveTypes'));
    }

    public function approve(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        
        // Set the status explicitly to 'approved'
        $status = 'approved';
        
        // Check if it's an annual leave
        if ($leave->leaveType->code === 'AL') {
            $annualLeaveController = new AnnualLeaveBalanceController();
            
            // Try to deduct the leave days
            $deductionSuccess = $annualLeaveController->deductAnnualLeave(
                $leave->user_id, 
                $leave->number_of_days
            );
            
            if (!$deductionSuccess) {
                return back()->with('error', 'Insufficient annual leave balance.');
            }
        }

        $leave->status = $status;
        $leave->save();

        return back()->with('success', 'Leave status updated successfully.');
    }

    public function reject(Leave $leave)
    {
        $leave->status = 'rejected';
        $leave->save();
        return redirect()->back()->with('success', 'Leave rejected successfully.');
    }

    public function show(Leave $leave)
    {
        return view('admin.leave.show', compact('leave'));
    }

    public function leaveBalance(Request $request)
    {
        $query = Leave::with(['user', 'leaveType']);

        // Apply filters if they are present in the request
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginate the results
        $leaves = $query->paginate(10);

        // Get all users and leave types for the filters
        $users = User::all();

        return view('admin.leave.leave-balance', compact('leaves', 'users'));
    }

    public function export()
    {
        try {
            $leaves = Leave::with(['user', 'leaveType'])->get();
            
            $filename = "leaves_" . date('Y-m-d_His') . ".csv";
            
            $csvData = [];
            
            // Headers
            $csvData[] = [
                'Employee Name',
                'Leave Type',
                'From Date',
                'To Date',
                'Number of Days',
                'Reason',
                'Status',
                'Applied Date'
            ];
            
            // Data
            foreach ($leaves as $leave) {
                $fromDate = $leave->from_date instanceof \Carbon\Carbon 
                    ? $leave->from_date->format('d/m/Y') 
                    : date('d/m/Y', strtotime($leave->from_date));
                    
                $toDate = $leave->to_date instanceof \Carbon\Carbon 
                    ? $leave->to_date->format('d/m/Y') 
                    : date('d/m/Y', strtotime($leave->to_date));
                    
                $appliedDate = $leave->created_at instanceof \Carbon\Carbon 
                    ? $leave->created_at->format('d/m/Y') 
                    : date('d/m/Y', strtotime($leave->created_at));

                $csvData[] = [
                    $leave->user->username ?? 'N/A',
                    $leave->leaveType->leave_type,
                    $fromDate,
                    $toDate,
                    $leave->number_of_days,
                    $leave->reason ?? '-',
                    ucfirst($leave->status),
                    $appliedDate
                ];
            }
            
            $handle = fopen('php://temp', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel
            
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);
            
            return response($csv)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            $import = new LeaveImport;
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            
            if (count($errors) > 0) {
                return redirect()
                    ->route('admin.leave.index')
                    ->with('importErrors', $errors)
                    ->with('success', 'Import completed with some errors.');
            }

            return redirect()
                ->route('admin.leave.index')
                ->with('success', 'All records imported successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.leave.index')
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=leave_import_template.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

            // Add header row - matching the actual Excel columns
            fputcsv($file, [
                'employee_name',
                'leave_type',
                'from_date',
                'to_date',
                'number_of_days',
                'reason',
                'status',
                'applied_date'
            ]);

            // Add sample row
            fputcsv($file, [
                'john_doe',
                'Annual Leave',
                '01/01/2024',
                '02/01/2024',
                '2',
                'Vacation',
                'Pending',
                '01/01/2024'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getPublicHolidays($year, $month)
    {
        try {
            $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/{$year}/MY");

            if ($response->successful()) {
                $holidays = $response->json();
                
                // Filter holidays for the current month and transform the data
                return collect($holidays)
                    ->filter(function ($holiday) use ($month) {
                        return Carbon::parse($holiday['date'])->month == $month;
                    })
                    ->mapWithKeys(function ($holiday) {
                        return [
                            $holiday['date'] => [
                                'name' => $holiday['localName'],
                                'description' => $holiday['name'],
                                'type' => 'holiday'
                            ]
                        ];
                    });
            }
            
            return collect([]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch public holidays: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function generateCalendar($month = null, $year = null)
    {
        $month = $month ?? date('n');
        $year = $year ?? date('Y');
        
        // Get the first day of the month
        $firstDay = Carbon::create($year, $month, 1);
        
        // Get today's date for comparison
        $today = Carbon::today();
        
        // Get the first day of the calendar (might be in previous month)
        $start = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);
        
        // Get the last day of the calendar (might be in next month)
        $end = $firstDay->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        
        // Get public holidays
        $publicHolidays = $this->getPublicHolidays($year, $month);
        
        // Get all leaves for the visible calendar period
        $leaves = Leave::where(function($query) use ($start, $end) {
            $query->whereBetween('from_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                  ->orWhereBetween('to_date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);
        })
        ->with(['user', 'leaveType'])
        ->get();
        
        $calendar = [];
        $currentDate = $start->copy();
        
        // Generate calendar data
        while ($currentDate <= $end) {
            $date = $currentDate->format('Y-m-d');
            
            // Get leaves for this day
            $dayLeaves = $leaves->filter(function($leave) use ($currentDate) {
                $fromDate = Carbon::parse($leave->from_date);
                $toDate = Carbon::parse($leave->to_date);
                return $currentDate->between($fromDate, $toDate);
            });
            
            $calendar[] = [
                'day' => $currentDate->format('j'),
                'date' => $date,
                'isCurrentMonth' => $currentDate->month == $month,
                'isToday' => $currentDate->isToday(),
                'leaves' => $dayLeaves,
                'holiday' => $publicHolidays->get($date)
            ];
            
            $currentDate->addDay();
        }
        
        return $calendar;
    }

    public function calendar(Request $request)
    {
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);

        // Generate months array
        $months = [
            ['value' => 1, 'name' => 'January'],
            ['value' => 2, 'name' => 'February'],
            ['value' => 3, 'name' => 'March'],
            ['value' => 4, 'name' => 'April'],
            ['value' => 5, 'name' => 'May'],
            ['value' => 6, 'name' => 'June'],
            ['value' => 7, 'name' => 'July'],
            ['value' => 8, 'name' => 'August'],
            ['value' => 9, 'name' => 'September'],
            ['value' => 10, 'name' => 'October'],
            ['value' => 11, 'name' => 'November'],
            ['value' => 12, 'name' => 'December']
        ];

        // Generate years array (e.g., current year -2 to +2)
        $years = range(now()->year - 2, now()->year + 2);

        // Get prev month and year
        if ($currentMonth == 1) {
            $prevMonth = 12;
            $prevYear = $currentYear - 1;
        } else {
            $prevMonth = $currentMonth - 1;
            $prevYear = $currentYear;
        }

        // Get next month and year
        if ($currentMonth == 12) {
            $nextMonth = 1;
            $nextYear = $currentYear + 1;
        } else {
            $nextMonth = $currentMonth + 1;
            $nextYear = $currentYear;
        }

        // Format current month name
        $monthName = date('F', mktime(0, 0, 0, $currentMonth, 1));

        $calendar = $this->generateCalendar($currentMonth, $currentYear);
        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('admin.leave.calendar', compact(
            'calendar', 
            'leaveTypes', 
            'monthName', 
            'currentYear',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'months',
            'years',
            'currentMonth'
        ));
    }
}