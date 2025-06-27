<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\AnnualLeaveBalance;
use App\Models\LeavePrediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeaveImport;
use Illuminate\Support\Facades\Log;

class LeaveController extends Controller
{
    use AuthorizesRequests;

    // ===================== Create Leave =======================
    public function create()
    {
        $users = User::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();
        return view('admin.leave.create', compact('users', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_type,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $annualBalance = AnnualLeaveBalance::where('user_id', $validated['user_id'])->first();
        $leaveType = LeaveType::find($validated['leave_type_id']);

        if ($leaveType && $leaveType->code === 'AL') {
            if (!$annualBalance || $annualBalance->annual_leave_balance < $validated['number_of_days']) {
                return back()->with('error', 'Not enough annual leave balance.');
            }
        }

        Leave::create([
            ...$validated,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave created successfully.');
    }

    // ===================== Index with Filters =======================
    public function index(Request $request)
    {
        $query = Leave::with(['user', 'leaveType'])
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            });

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('year')) {
            $query->whereYear('from_date', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('from_date', $request->month);
        }
        if ($request->filled('filter_date')) {
            $query->whereDate('from_date', $request->filter_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        $users = User::where('status', 'active')->get();
        $leaveTypes = LeaveType::all();
        $predictions = LeavePrediction::whereBetween('date', [now(), now()->addDays(30)])->get();

        return view('admin.leave.index', compact('leaves', 'users', 'leaveTypes', 'predictions'));
    }

    // ===================== Edit and Update =======================
    public function edit($id)
    {
        $leave = Leave::with(['user', 'leaveType'])->findOrFail($id);
        $users = User::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();
        return view('admin.leave.edit', compact('leave', 'users', 'leaveTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_type,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'number_of_days' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave = Leave::findOrFail($id);
        $leaveType = LeaveType::find($validated['leave_type_id']);

        if ($leaveType && $leaveType->code === 'AL' && $validated['status'] === 'approved' && $leave->status !== 'approved') {
            $balance = AnnualLeaveBalance::where('user_id', $validated['user_id'])->first();
            if (!$balance || $balance->annual_leave_balance < $validated['number_of_days']) {
                return back()->with('error', 'Not enough annual leave balance to approve this leave.');
            }
            $balance->annual_leave_balance -= $validated['number_of_days'];
            $balance->save();
        }

        $leave->update($validated);

        return redirect()->route('admin.leave.index')->with('success', 'Leave updated successfully.');
    }

    // ===================== Delete =======================
    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status === 'approved' && $leave->leaveType->code === 'AL') {
            $balance = AnnualLeaveBalance::where('user_id', $leave->user_id)->first();
            if ($balance) {
                $balance->annual_leave_balance += $leave->number_of_days;
                $balance->save();
            }
        }

        $leave->delete();

        return back()->with('success', 'Leave deleted successfully.');
    }

    // ===================== Process (Approve/Reject) =======================
    public function processLeaves(Request $request)
    {
        $query = Leave::with(['user', 'leaveType'])
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->where('status', 'pending');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }
        if ($request->filled('filter_date')) {
            $query->whereDate('from_date', $request->filter_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        $users = User::where('status', 'active')->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        return view('admin.leave.process', compact('leaves', 'users', 'leaveTypes'));
    }

    public function approve(Leave $leave)
    {
        \Log::info('Approve called for leave ID: ' . $leave->id);
        if ($leave->leaveType->code === 'AL') {
            $balance = AnnualLeaveBalance::where('user_id', $leave->user_id)->first();
            if (!$balance || $balance->annual_leave_balance < $leave->number_of_days) {
                return back()->with('error', 'Insufficient annual leave balance.');
            }
            $balance->annual_leave_balance -= $leave->number_of_days;
            $balance->save();
        }

        $leave->status = 'approved';
        $leave->save();

        return back()->with('success', 'Leave approved successfully.');
    }

    public function reject(Leave $leave)
    {
        $leave->status = 'rejected';
        $leave->save();
        return back()->with('success', 'Leave rejected successfully.');
    }

    public function show(Leave $leave)
    {
        return view('admin.leave.show', compact('leave'));
    }

    // ===================== Export & Import =======================
    public function export()
    {
        try {
            $leaves = Leave::with(['user', 'leaveType'])
                ->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })
                ->where('status', 'active')
                ->get();
            $filename = "leaves_" . now()->format('Ymd_His') . ".csv";

            $csv = fopen('php://temp', 'w+');
            fputs($csv, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            fputcsv($csv, [
                'Employee Name',
                'Leave Type',
                'From Date',
                'To Date',
                'Number of Days',
                'Reason',
                'Status',
                'Applied Date'
            ]);

            foreach ($leaves as $leave) {
                fputcsv($csv, [
                    $leave->user->username ?? 'N/A',
                    $leave->leaveType->leave_type ?? 'N/A',
                    Carbon::parse($leave->from_date)->format('d/m/Y'),
                    Carbon::parse($leave->to_date)->format('d/m/Y'),
                    $leave->number_of_days,
                    $leave->reason ?? '-',
                    ucfirst($leave->status),
                    Carbon::parse($leave->created_at)->format('d/m/Y')
                ]);
            }

            rewind($csv);
            $content = stream_get_contents($csv);
            fclose($csv);

            return response($content)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', "attachment; filename={$filename}");
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return back()->with('error', 'Export failed.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            $import = new LeaveImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            if (count($errors) > 0) {
                return back()->with('importErrors', $errors)->with('success', 'Import completed with some errors.');
            }

            return back()->with('success', 'Import successful.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=leave_import_template.csv',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

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

    // ===================== Calendar =======================
    private function getPublicHolidays($year, $month)
    {
        try {
            $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/{$year}/MY");

            if ($response->successful()) {
                return collect($response->json())->filter(fn($holiday) => Carbon::parse($holiday['date'])->month == $month)
                    ->mapWithKeys(fn($holiday) => [$holiday['date'] => $holiday]);
            }
            return collect();
        } catch (\Exception $e) {
            Log::error('Fetch public holidays error: ' . $e->getMessage());
            return collect();
        }
    }

    private function generateCalendar($month, $year)
    {
        $firstDay = Carbon::create($year, $month, 1);
        $start = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);
        $end = $firstDay->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        $holidays = $this->getPublicHolidays($year, $month);

        $leaves = Leave::where(function ($q) use ($start, $end) {
            $q->whereBetween('from_date', [$start, $end])
                ->orWhereBetween('to_date', [$start, $end]);
        })->with(['user', 'leaveType'])
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->where('status', 'active')
            ->get();

        $calendar = [];
        $current = $start->copy();

        while ($current <= $end) {
            $calendar[] = [
                'day' => $current->format('j'),
                'date' => $current->format('Y-m-d'),
                'isCurrentMonth' => $current->month == $month,
                'isToday' => $current->isToday(),
                'leaves' => $leaves->filter(fn($leave) => Carbon::parse($leave->from_date)->lte($current) && Carbon::parse($leave->to_date)->gte($current)),
                'holiday' => $holidays->get($current->format('Y-m-d'))
            ];
            $current->addDay();
        }

        return $calendar;
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $calendar = $this->generateCalendar($month, $year);
        $leaveTypes = LeaveType::where('status', 'active')->get();

        $months = collect(range(1, 12))->map(fn($m) => ['value' => $m, 'name' => date('F', mktime(0, 0, 0, $m, 10))]);
        $years = range(now()->year - 2, now()->year + 2);

        return view('admin.leave.calendar', compact(
            'calendar', 'leaveTypes', 'month', 'year', 'months', 'years'
        ));
    }
}
