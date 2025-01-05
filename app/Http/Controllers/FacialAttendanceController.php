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

    public function verifyFaceView()
    {
        return view('attendance.verify-face');
    }

    public function registerFace(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'faceDescriptor' => 'required|array',
            ]);

            // 通过 username 查找用户
            $user = User::where('username', $request->username)->firstOrFail();

            // Update the user's face descriptor
            $user->update([
                'face_descriptor' => json_encode($request->faceDescriptor),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Face registered successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error registering face: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyFace(Request $request)
    {
        try {
            $request->validate([
                'descriptor' => 'required|array'
            ]);

            // 获取当前用户的面部特征
            $user = auth()->user();
            if (!$user || !$user->face_descriptor) {
                return response()->json([
                    'verified' => false,
                    'message' => 'No registered face found'
                ]);
            }

            // 将存储的面部特征转换回数组
            $storedDescriptor = json_decode($user->face_descriptor);
            
            // 计算欧氏距离来比较面部特征
            $distance = $this->calculateDistance(
                $request->descriptor,
                $storedDescriptor
            );

            // 设置阈值（可以根据需要调整）
            $threshold = 0.6;

            // 检查是否可以打卡
            $lastAttendance = Attendance::where('user_id', $user->id)
                ->latest()
                ->first();
            
            $canClockIn = !$lastAttendance || $lastAttendance->type === 'out';
            $canClockOut = $lastAttendance && $lastAttendance->type === 'in';

            return response()->json([
                'verified' => $distance < $threshold,
                'canClockIn' => $canClockIn,
                'canClockOut' => $canClockOut,
                'message' => $distance < $threshold ? 'Face verified' : 'Face not recognized'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'verified' => false,
                'message' => 'Error verifying face: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateDistance($descriptor1, $descriptor2)
    {
        return array_sum(array_map(function($a, $b) {
            return ($a - $b) * ($a - $b);
        }, $descriptor1, $descriptor2));
    }
}
