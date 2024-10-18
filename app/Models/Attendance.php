<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // Define which attributes can be mass assigned
    protected $fillable = [
        'user_id', 'attendance_date', 'attendance_time', 'status',
    ];

    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
