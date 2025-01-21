<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Fingerprint;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use GuzzleHttp\Client;

class FingerprintController extends Controller
{
    public function index()
    {
        $employees = User::with('fingerprint')
            ->select('users.*')
            ->addSelect([
                'hasFingerprint' => Fingerprint::selectRaw('COUNT(*)')
                    ->whereColumn('user_id', 'users.id')
                    ->limit(1),
                'fingerprint_updated_at' => Fingerprint::select('updated_at')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1)
            ])
            ->paginate(10);

        // Convert fingerprint_updated_at to Carbon instance
        $employees->each(function ($employee) {
            if ($employee->fingerprint_updated_at) {
                $employee->fingerprint_updated_at = Carbon::parse($employee->fingerprint_updated_at);
            }
        });

        return view('admin.fingerprint.index', compact('employees'));
    }

    // Show the page for enrolling fingerprints
    public function enrollPage($id)
    {
        $employee = User::findOrFail($id);  // Get specific user
        return view('admin.fingerprint.enroll', compact('employee'));
    }

    // Handle the enrollment of fingerprints
    public function enrollFingerprint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint1' => 'required|image',
            'fingerprint2' => 'required|image',
            'fingerprint3' => 'required|image',
            'fingerprint4' => 'required|image',
            'fingerprint5' => 'required|image',
        ]);

        try {
            // Store all fingerprint images
            $paths = [];
            for ($i = 1; $i <= 5; $i++) {
                $paths["fingerprint_image{$i}"] = $request->file("fingerprint{$i}")->store('fingerprints');
            }

            // Check if user already has fingerprint data
            $existingFingerprint = Fingerprint::where('user_id', $request->user_id)->first();

            if ($existingFingerprint) {
                // Delete old fingerprint images
                Storage::delete([
                    $existingFingerprint->fingerprint_image,
                    $existingFingerprint->fingerprint_image2,
                    $existingFingerprint->fingerprint_image3,
                    $existingFingerprint->fingerprint_image4,
                    $existingFingerprint->fingerprint_image5,
                ]);

                // Update existing record
                $existingFingerprint->update([
                    'fingerprint_image' => $paths['fingerprint_image1'],
                    'fingerprint_image2' => $paths['fingerprint_image2'],
                    'fingerprint_image3' => $paths['fingerprint_image3'],
                    'fingerprint_image4' => $paths['fingerprint_image4'],
                    'fingerprint_image5' => $paths['fingerprint_image5'],
                    'status' => 'active',
                ]);

                $message = 'Fingerprint templates updated successfully!';
            } else {
                // Create new record
                Fingerprint::create([
                    'user_id' => $request->user_id,
                    'fingerprint_image' => $paths['fingerprint_image1'],
                    'fingerprint_image2' => $paths['fingerprint_image2'],
                    'fingerprint_image3' => $paths['fingerprint_image3'],
                    'fingerprint_image4' => $paths['fingerprint_image4'],
                    'fingerprint_image5' => $paths['fingerprint_image5'],
                    'status' => 'active',
                ]);

                $message = 'Fingerprint enrolled successfully!';
            }

            return redirect()->route('fingerprint.index')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Fingerprint enrollment error:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()->with('error', 'Enrollment error: ' . $e->getMessage());
        }
    }

    // Show the page for verifying fingerprints (clock in/out)
    public function verifyPage()
    {
        $employees = User::all();  // Get all users without role filtering
        return view('admin.fingerprint.verify', compact('employees'));
    }

    // Handle the fingerprint verification (clock in/out)
    public function verifyFingerprint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint' => 'required|image',
        ]);

        $user = User::findOrFail($request->user_id);
        $storedFingerprint = Fingerprint::where('user_id', $request->user_id)
                                      ->where('status', 'active')
                                      ->firstOrFail();

        try {
            // Re-enroll the fingerprints if they don't exist
            if (!Storage::exists($storedFingerprint->fingerprint_image)) {
                return redirect()->back()->with('error', 'Fingerprint templates not found. Please re-enroll.');
            }

            // Prepare multipart request
            $multipart = [
                [
                    'name' => 'fingerprint',
                    'contents' => fopen($request->file('fingerprint')->path(), 'r')
                ]
            ];

            // Add templates to request
            $templatePaths = [
                $storedFingerprint->fingerprint_image,
                $storedFingerprint->fingerprint_image2,
                $storedFingerprint->fingerprint_image3,
                $storedFingerprint->fingerprint_image4,
                $storedFingerprint->fingerprint_image5,
            ];

            foreach ($templatePaths as $i => $path) {
                $fullPath = Storage::path($path);
                $multipart[] = [
                    'name' => "template" . ($i + 1),
                    'contents' => fopen($fullPath, 'r')
                ];
            }

            // Make API request
            $client = new \GuzzleHttp\Client();
            $response = $client->post('http://127.0.0.1:5000/verify', [
                'multipart' => $multipart
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result['match']) {
                // Set Malaysia timezone
                date_default_timezone_set('Asia/Kuala_Lumpur');
                $serverTime = Carbon::now('Asia/Kuala_Lumpur');
                $today = $serverTime->toDateString();

                // Get last attendance record
                $lastAttendance = Attendance::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->latest()
                    ->first();

                // Get user's schedule
                $schedule = Schedule::where('user_id', $user->id)
                    ->whereDate('shift_date', $today)
                    ->first();

                if (!$schedule) {
                    return redirect()->back()->with('error', 'No schedule found for today');
                }

                if (!$lastAttendance || ($lastAttendance->clock_in_time && $lastAttendance->clock_out_time)) {
                    // Clock In
                    $scheduledStart = Carbon::parse($today . ' ' . $schedule->start_time);
                    $lateThreshold = $scheduledStart->copy()->addMinutes(15);
                    $status = $serverTime->lt($lateThreshold) ? 'on_time' : 'late';

                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $today,
                        'clock_in_time' => $serverTime,
                        'status' => $status,
                        'device_time' => $serverTime->format('H:i:s')
                    ]);

                    $message = 'Clock-in successful! ';
                    $message .= $status === 'late' ? '(Late)' : '(On Time)';
                } else {
                    // Clock Out
                    $lastAttendance->clock_out_time = $serverTime;
                    $lastAttendance->save();

                    $message = 'Clock-out successful!';
                }

                return redirect()->back()->with('success', 
                    $message . ' (Confidence: ' . number_format($result['confidence'], 2) . '%)');
            }

            return redirect()->back()->with('error', 
                'Fingerprint verification failed! (Confidence: ' . number_format($result['confidence'], 2) . '%)');

        } catch (\Exception $e) {
            \Log::error('Fingerprint verification error:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()->with('error', 'Verification service error: ' . $e->getMessage());
        }
    }

    public function removeFingerprint($id)
    {
        try {
            $fingerprint = Fingerprint::where('user_id', $id)->firstOrFail();

            // Delete fingerprint images from storage
            Storage::delete([
                $fingerprint->fingerprint_image,
                $fingerprint->fingerprint_image2,
                $fingerprint->fingerprint_image3,
                $fingerprint->fingerprint_image4,
                $fingerprint->fingerprint_image5,
            ]);

            // Delete the fingerprint record
            $fingerprint->delete();

            return redirect()->back()->with('success', 'Fingerprint removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Fingerprint removal error:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()->with('error', 'Error removing fingerprint: ' . $e->getMessage());
        }
    }
}
