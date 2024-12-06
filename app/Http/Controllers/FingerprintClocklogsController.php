<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FingerprintClocklogs;
use Illuminate\Http\Request;

class FingerprintClocklogsController extends Controller
{
    public function showClockInOutPage()
    {
        return view('admin.fingerprint_clocklogs.clock_in_out');
    }

    public function showAddFingerprintPage()
    {
        $users = User::all();
        return view('admin.fingerprint_clocklogs.add_fingerprint', compact('users'));
    }

    // Clock In and Clock Out Function
    public function clockInOut(Request $request)
    {
        try {
            $request->validate([
                'fingerprint_id' => 'required|exists:users,fingerprint_id'
            ]);

            $user = User::where('fingerprint_id', $request->fingerprint_id)->first();
            
            // Get the last clock record for this user
            $lastRecord = FingerprintClocklogs::where('user_id', $user->id)
                ->latest()
                ->first();

            // Determine if this should be a clock in or clock out
            $type = (!$lastRecord || $lastRecord->type === 'clockout') ? 'clockin' : 'clockout';

            // Create new clock record
            FingerprintClocklog::create([
                'user_id' => $user->id,
                'type' => $type,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully {$type}ed",
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Clock in/out failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    // Add Fingerprint to Database
    public function addFingerprint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint_id' => 'required|string|unique:users,fingerprint_id'
        ]);

        // Instead of creating a new fingerprint record, update the user
        $user = User::findOrFail($request->user_id);
        $user->fingerprint_id = $request->fingerprint_id;
        $user->save();

        return redirect()->route('admin.fingerprint_clocklogs.add_fingerprint')
            ->with('success', 'Fingerprint added successfully!');
    }

    // Helper function to toggle between clock-in and clock-out
    protected function toggleClockInOut($user)
    {
        // Example logic for toggling clock in/out status
        // You can check the last clock log or use any other custom logic
        $now = now();

        // Here, you would check the status of the user's clock log and update it accordingly
        // For simplicity, assume it's clocking in if no prior log exists
        FingerprintClockLogs::create([
            'user_id' => $user->id,
            'type' => 'clock-in', // or clock-out based on the logic
            'timestamp' => $now,
        ]);

        return 'Clocked in successfully!';
    }

    public function scanFingerprint(Request $request)
    {
        try {
            // Simulate fingerprint scanning
            // This should ideally come from your fingerprint scanner device

            // Generate a base64-encoded fingerprint image for simulation
            $fingerprintImage = $this->generateSimulatedFingerprintImage();

            $data = [
                'success' => true,
                'fingerprint_id' => rand(1000, 9999),
                'fingerprint_data' => [
                    'minutiae' => [],
                    'pattern' => 'whorl',
                ],
                'image' => $fingerprintImage, // Include the base64 fingerprint image
                'quality' => rand(85, 100), // Quality score 0-100
                'position_x' => rand(0, 100),
                'position_y' => rand(0, 100),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to scan fingerprint',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate a simulated base64 fingerprint image.
     * In production, replace this with the actual image from the scanner.
     */
    private function generateSimulatedFingerprintImage()
    {
        $width = 300;
        $height = 400;

        // Create a blank image
        $image = imagecreate($width, $height);

        // Set background and fingerprint colors
        $bgColor = imagecolorallocate($image, 240, 240, 240);
        $lineColor = imagecolorallocate($image, 0, 0, 0);

        // Draw random fingerprint-like lines
        for ($i = 0; $i < 10; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        // Capture the image as a base64 string
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();

        // Free memory
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }


    public function storeFingerprint(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'fingerprint_id' => 'required|string'
            ]);

            $user = User::findOrFail($validated['user_id']);
            $user->fingerprint_id = $validated['fingerprint_id'];
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint stored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store fingerprint',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'fingerprint_id' => 'required|string',
            'timestamp' => 'required|date',
        ]);

        // Store fingerprint log in the database
        $fingerprintLog = FingerprintClocklogs::create([
            'fingerprint_id' => $validated['fingerprint_id'],
            'timestamp' => $validated['timestamp'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint stored successfully',
            'data' => $fingerprintLog,
        ]);
    }

    public function checkScannerStatus()
    {
        try {
            // Add your scanner connection check logic here
            // This is just an example - implement according to your scanner's SDK
            $isConnected = false; // Set this based on your scanner check
            
            return response()->json([
                'isConnected' => $isConnected,
                'message' => $isConnected ? 'Scanner connected' : 'Scanner not connected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'isConnected' => false,
                'message' => 'Error checking scanner: ' . $e->getMessage()
            ]);
        }
    }
}
