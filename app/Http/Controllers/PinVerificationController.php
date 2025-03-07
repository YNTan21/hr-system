<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PinVerificationController extends Controller
{
    public function verify(Request $request)
    {
        try {
            $pin = $request->pin;
            $systemPin = Setting::where('key', 'system_pin')->first();
            
            Log::info('Verification attempt', [
                'input_pin' => $pin,
                'system_pin' => $systemPin ? $systemPin->value : null
            ]);

            if (!$systemPin) {
                return response()->json([
                    'success' => false,
                    'message' => 'System PIN not configured'
                ], 400);
            }

            // Direct string comparison
            $isValid = ($pin === $systemPin->value);
            
            Log::info('Verification result', ['is_valid' => $isValid]);

            // Return success response if PIN matches
            if ($isValid) {
                return response()->json([
                    'success' => true,
                    'message' => 'PIN verified successfully'
                ], 200);
            }

            // Return error response if PIN doesn't match
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN'
            ], 401);

        } catch (\Exception $e) {
            Log::error('Verification error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
}
