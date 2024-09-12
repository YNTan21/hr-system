<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'number_of_days',
        'reason',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function leaveType() {
        return $this->belongsTo(LeaveType::class);
    }
}
