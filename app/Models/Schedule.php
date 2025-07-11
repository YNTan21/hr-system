<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'shift_date',
        'shift_code',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    // 获取格式化的开始时间
    public function getStartTimeAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    // 获取格式化的结束时间
    public function getEndTimeAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendanceSchedules()
    {
        return $this->hasMany(AttendanceSchedule::class, 'schedules_id');
    }    
} 
