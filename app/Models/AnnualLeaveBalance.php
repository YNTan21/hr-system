<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\User;

class AnnualLeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'annual_leave_balance',
        'approved_leave_days',
    ];

    // Define the relationship with the Employee model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming user_id is the foreign key
    }

    // Method to deduct approved leave days
    public function deductLeaveDays($days)
    {
        if ($this->annual_leave_balance >= $days) {
            $this->annual_leave_balance -= $days;
            $this->save();
            return true;
        }
        return false; // Not enough balance to deduct
    }
}