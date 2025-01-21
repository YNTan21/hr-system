<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class PinVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $pin = $request->pin;
        $systemPin = Setting::where('key', 'system_pin')->first()->value;

        return response()->json([
            'success' => $pin === $systemPin
        ]);
    }
} 