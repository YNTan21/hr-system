<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'shift_date',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendanceSchedules()
    {
        return $this->hasMany(AttendanceSchedule::class, 'schedules_id');
    }    
} 
