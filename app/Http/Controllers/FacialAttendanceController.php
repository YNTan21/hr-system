<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Position;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FacialAttendanceController
{
    public function recordAttendance(Request $request)
    {
        try {
            $type = $request->input('type');
            
            // Record the attendance in your database
            $attendance = new Attendance([
                'type' => $type,
                'timestamp' => now(),
                // Add other necessary fields
            ]);
            
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => "Successfully clocked " . ($type === 'in' ? 'in' : 'out')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording attendance: ' . $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        return view('attendance.facial-recognition');
    }
}
