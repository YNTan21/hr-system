<?php

namespace App\Imports;

use App\Models\Leave;
use App\Models\User;
use App\Models\LeaveType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaveImport implements ToModel, WithHeadingRow, WithStartRow
{
    private $errors = [];
    private $currentRow = 1;

    public function model(array $row)
    {
        $this->currentRow++;
        
        try {
            // Log the row data
            Log::info('Processing row:', [
                'row_number' => $this->currentRow,
                'data' => $row
            ]);

            // Validate required fields
            $requiredFields = [
                'employee_name',
                'leave_type',
                'from_date',
                'to_date',
                'number_of_days'
            ];

            foreach ($requiredFields as $field) {
                if (!isset($row[$field])) {
                    throw new \Exception("Missing required field: {$field}. Available fields: " . implode(', ', array_keys($row)));
                }
            }

            // Find user by username
            $user = User::where('username', $row['employee_name'])->first();
            if (!$user) {
                throw new \Exception("User not found: {$row['employee_name']}");
            }

            // Find leave type
            $leaveType = LeaveType::where('leave_type', $row['leave_type'])->first();
            if (!$leaveType) {
                throw new \Exception("Leave type not found: {$row['leave_type']}");
            }

            // Parse dates
            $fromDate = Carbon::createFromFormat('d/m/Y', $row['from_date']);
            $toDate = Carbon::createFromFormat('d/m/Y', $row['to_date']);

            // Validate dates
            if ($toDate < $fromDate) {
                throw new \Exception("To Date must be after From Date");
            }

            return new Leave([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'number_of_days' => $row['number_of_days'],
                'reason' => $row['reason'] ?? null,
                'status' => strtolower($row['status'] ?? 'pending'),
            ]);

        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->currentRow,
                'error' => $e->getMessage()
            ];
            return null;
        }
    }

    public function startRow(): int
    {
        return 2; // Start from row 2 (after headers)
    }

    public function getErrors()
    {
        return $this->errors;
    }
} 