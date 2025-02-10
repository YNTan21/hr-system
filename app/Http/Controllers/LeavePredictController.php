<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeavePrediction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeavePredictController extends Controller
{
    public function getLeaveData()
    {
        // Get historical leave data
        $leaves = Leave::select('from_date', 'number_of_days')
            ->where('status', 'approved')
            ->get()
            ->map(function ($leave) {
                return [
                    'from_date' => $leave->from_date,
                    'number_of_days' => $leave->number_of_days
                ];
            });

        return response()->json($leaves);
    }

    public function predict()
    {
        try {
            $pythonScript = base_path('flask-api/predict_leave.py');
            
            // Log the command being executed
            \Log::info("Executing command: python3 " . escapeshellarg($pythonScript));
            
            $command = "python3 " . escapeshellarg($pythonScript) . " 2>&1";
            $output = shell_exec($command);

            // Log the output
            \Log::info("Python script output: " . $output);

            if ($output === null) {
                throw new \Exception("Failed to execute Python script");
            }

            $predictions = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON output from Python script: " . json_last_error_msg());
            }

            // Store predictions in database
            foreach ($predictions as $prediction) {
                LeavePrediction::updateOrCreate(
                    ['date' => $prediction['date']],
                    ['predicted_leaves' => $prediction['predicted_leaves']]
                );
            }

            return response()->json([
                'success' => true,
                'predictions' => $predictions
            ]);

        } catch (\Exception $e) {
            \Log::error("Prediction error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storePredictions(Request $request)
    {
        try {
            $predictions = $request->all();
            
            foreach ($predictions as $prediction) {
                LeavePrediction::updateOrCreate(
                    ['date' => $prediction['date']], 
                    ['predicted_leaves' => $prediction['predicted_leaves']]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Predictions saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showPredictions()
    {
        // Get predictions for the next 30 days
        $predictions = LeavePrediction::where('date', '>=', now())
            ->where('date', '<=', now()->addDays(30))
            ->orderBy('date')
            ->get();

        // Debug log
        \Log::info('Fetched predictions:', ['count' => $predictions->count(), 'data' => $predictions->toArray()]);

        return view('admin.leave.predictions', compact('predictions'));
    }
}
