<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'leave_type',
        'from_date',
        'to_date',
        'number_of_days',
        'reason',
    ];

    // Define relationships

    // Relationship: Leave belongs to a user (assuming employee_name refers to a user)
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_name');
    }

    // Relationship: Leave belongs to a leave type
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type');
    }
}
