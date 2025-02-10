<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeavePrediction;

class PredictLeaves extends Command
{
    protected $signature = 'leave:predict';
    protected $description = 'Generate leave predictions for the next 30 days';

    public function handle()
    {
        try {
            $pythonScript = base_path('flask-api/predict_leave.py');
            $command = "python3 " . escapeshellarg($pythonScript) . " 2>&1";
            $output = shell_exec($command);

            if ($output === null) {
                $this->error("Failed to execute Python script");
                return 1;
            }

            $this->info("Predictions generated successfully");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
} 