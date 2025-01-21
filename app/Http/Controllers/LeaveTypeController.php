<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('admin.leaveType.index', ['leaveTypes' => $leaveTypes]);
    }
    
    public function store(Request $request)
    {
        
        $request->validate([
            'leaveType' => 'required|string|max:255',
            'leaveCode' => 'required|string|max:8',
            'status' => 'required|in:active,inactive'
        ]);

        $fields = [
            'leave_type' => $request->leaveType,
            'code' => $request->leaveCode,
            'status' => $request->status,
            'deduct_annual_leave' => $request->has('deductAnnualLeave'),
        ];

        LeaveType::create($fields);
        // Auth::user()->posts()->store($fields);

        return redirect()->route('admin.leaveType.index')->with('success', 'Leave Type created successfully.');
    }

    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('admin.leaveType.edit', compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        $request -> validate([
            'leaveType' => 'required|string|max:255',
            'leaveCode' => 'required|string|max:8',
            'status' => 'required|in:active,inactive'
        ]);

        $leaveType = LeaveType::findOrFail($id);

        $leaveType->update([
            'leave_type' => $request->leaveType,
            'code' => $request->leaveCode,
            'status' => $request->status,
            'deduct_annual_leave' => $request->has('deductAnnualLeave'),
        ]);

        return redirect()->route('admin.leaveType.index')->with('success', 'Leave Type updated successfully.');
    }

    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->delete();

        return redirect()->route('admin.leaveType.index')->with('success', 'Leave Type deleted successfully.');
    }

    public function export()
    {
        try {
            $leaveTypes = LeaveType::all();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=leave_types_report.csv',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public'
            ];

            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add BOM for Excel

            // Write headers
            fputcsv($output, [
                'Leave Type ID',
                'Leave Type Name',
                'Leave Code',
                'Status',
                'Deduct Annual Leave',
                'Created At'
            ]);

            // Write data
            foreach ($leaveTypes as $leaveType) {
                $createdAt = Carbon::parse($leaveType->created_at)->format('d/m/Y');

                fputcsv($output, [
                    "=\"{$leaveType->id}\"",
                    $leaveType->leave_type,
                    $leaveType->code,
                    ucfirst($leaveType->status),
                    $leaveType->deduct_annual_leave ? 'Yes' : 'No',
                    "=\"$createdAt\""
                ]);
            }

            fclose($output);

            return response()->stream(
                function() {
                    // Data has already been written to output
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
}
