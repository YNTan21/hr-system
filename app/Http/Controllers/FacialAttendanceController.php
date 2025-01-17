<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Schedule;
use App\Models\LeaveBalance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use Carbon\Carbon;

class FacialAttendanceController extends Controller
{
    public function recordAttendance(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:in,out',
                'username' => 'required|string',
                'local_time' => 'required|string'
            ]);

            // 设置马来西亚时区
            date_default_timezone_set('Asia/Kuala_Lumpur');
            $serverTime = Carbon::now('Asia/Kuala_Lumpur');
            $today = $serverTime->toDateString();

            $user = User::where('username', $request->username)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // 检查最近的考勤记录
            $lastAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->latest()
                ->first();

            if ($request->type === 'in') {
                // 如果最后一条记录是 clock in 且还没有 clock out
                if ($lastAttendance && $lastAttendance->clock_in_time && !$lastAttendance->clock_out_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already clocked in. Please clock out first.',
                        'last_clock_in' => $lastAttendance->clock_in_time->format('Y-m-d H:i:s'),
                        'server_time' => $serverTime->format('Y-m-d H:i:s')  // 添加服务器时间
                    ], 400);
                }

                $schedule = Schedule::where('user_id', $user->id)
                    ->whereDate('shift_date', $today)
                    ->first();

                if (!$schedule) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No schedule found for today',
                        'server_time' => $serverTime->format('Y-m-d H:i:s')  // 添加服务器时间
                    ], 404);
                }

                $scheduledStart = Carbon::parse($today . ' ' . $schedule->start_time);
                $lateThreshold = $scheduledStart->copy()->addMinutes(15);
                
                $status = $serverTime->lt($lateThreshold) ? 'on_time' : 'late';

                \Log::info('Attendance Time Check', [
                    'device_time' => $request->local_time,
                    'server_time' => $serverTime->format('Y-m-d H:i:s'),
                    'scheduled_start' => $scheduledStart->format('Y-m-d H:i:s'),
                    'late_threshold' => $lateThreshold->format('Y-m-d H:i:s'),
                    'status' => $status
                ]);

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'clock_in_time' => $serverTime,
                    'status' => $status,
                    'device_time' => $request->local_time
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Successfully clocked {$request->type}",
                    'data' => [
                        'server_time' => $serverTime->format('Y-m-d H:i:s'),
                        'status' => $status,
                        'attendance_id' => $attendance->id
                    ]
                ]);
            } 
            else if ($request->type === 'out') {
                // 如果没有未完成的 clock in 记录
                if (!$lastAttendance || $lastAttendance->clock_out_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please clock in first before clocking out.',
                        'server_time' => $serverTime->format('Y-m-d H:i:s')  // 添加服务器时间
                    ], 400);
                }

                $lastAttendance->clock_out_time = $serverTime;
                $lastAttendance->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully clocked out',
                    'data' => [
                        'server_time' => $serverTime->format('Y-m-d H:i:s')
                    ]
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Attendance Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'server_time' => $serverTime->format('Y-m-d H:i:s')  // 添加服务器时间到错误日志
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error recording attendance: ' . $e->getMessage(),
                'server_time' => $serverTime->format('Y-m-d H:i:s')  // 添加服务器时间
            ], 500);
        }
    }

    public function index()
    {
        $users = User::select('id', 'username')->get();
        return view('attendance.facial-recognition', compact('users'));
    }

    public function verifyFaceView()
    {
        $users = User::select('id', 'username')->get();
        return view('attendance.verify-face', compact('users'));
    }

    public function registerFace(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'faceDescriptor' => 'required|array'
            ]);

            $user = User::where('username', $request->username)->firstOrFail();
            
            // 检查用户已注册的面部特征数量
            $descriptorCount = $user->faceDescriptors()->count();
            $maxDescriptors = 5; // 最大允许的描述符数量
            
            if ($descriptorCount >= $maxDescriptors) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum number of face registrations ($maxDescriptors) reached."
                ]);
            }

            // 保存新的面部特征
            $user->faceDescriptors()->create([
                'descriptor' => json_encode($request->faceDescriptor)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Face registered successfully!',
                'descriptorCount' => $descriptorCount + 1,
                'maxDescriptors' => $maxDescriptors
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
                'descriptor' => 'required|array',
                'username' => 'required|string'
            ]);

            $user = User::where('username', $request->username)->first();
            
            if (!$user) {
                return response()->json([
                    'verified' => false,
                    'message' => 'User not found'
                ]);
            }

            // 获取用户所有的面部特征
            $storedDescriptors = $user->faceDescriptors()
                ->get()
                ->map(function($fd) {
                    return json_decode($fd->descriptor, true);
                });

            if ($storedDescriptors->isEmpty()) {
                return response()->json([
                    'verified' => false,
                    'message' => 'No registered faces found'
                ]);
            }

            // 计算与所有存储的特征的距离，取最小值
            $minDistance = PHP_FLOAT_MAX;
            foreach ($storedDescriptors as $storedDescriptor) {
                $distance = $this->calculateDistance($request->descriptor, $storedDescriptor);
                $minDistance = min($minDistance, $distance);
            }

            $threshold = 0.4;
            $verified = $minDistance < $threshold;

            \Log::info('Face verification details', [
                'user_id' => $user->id,
                'username' => $user->username,
                'min_distance' => $minDistance,
                'threshold' => $threshold,
                'verified' => $verified
            ]);

            return response()->json([
                'verified' => $verified,
                'canClockIn' => true,  // 始终允许打卡
                'canClockOut' => true, // 始终允许打卡
                'message' => $verified 
                    ? 'Face verified successfully' 
                    : sprintf('Face not recognized (distance: %.3f, threshold: %.3f)', $minDistance, $threshold),
                'debug' => [
                    'distance' => $minDistance,
                    'threshold' => $threshold
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Face verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'verified' => false,
                'canClockIn' => false,
                'canClockOut' => false,
                'message' => 'Error verifying face: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateDistance($descriptor1, $descriptor2)
    {
        if (count($descriptor1) !== count($descriptor2)) {
            throw new \Exception('Descriptor dimensions do not match');
        }

        $sum = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    public function getLastStatus($username)
    {
        try {
            $user = User::where('username', $username)->firstOrFail();
            $lastAttendance = Attendance::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->orderBy('created_at', 'desc')
                ->first();

            $canClockIn = !$lastAttendance || 
                ($lastAttendance->clock_in_time && $lastAttendance->clock_out_time);
            $canClockOut = $lastAttendance && 
                $lastAttendance->clock_in_time && 
                !$lastAttendance->clock_out_time;

            return response()->json([
                'canClockIn' => $canClockIn,
                'canClockOut' => $canClockOut
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'canClockIn' => true,
                'canClockOut' => false
            ]);
        }
    }
}
