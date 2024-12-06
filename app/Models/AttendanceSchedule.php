<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceSchedule extends Model
{
    use HasFactory;

    // 表名
    protected $table = 'attendance_schedules';

    // 可填充字段
    protected $fillable = [
        'schedules_id',
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'overtime_hour',
    ];

    public function attendance()
    {
        return $this->hasOne(AttendanceSchedule::class, 'schedules_id');
    }

    // 与 Schedule 模型的关联
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedules_id');
    }

    // 与 User 模型的关联
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 设置 clock_in 和 clock_out 为 Carbon 实例
    protected $dates = [
        'clock_in',
        'clock_out',
    ];

    // 状态计算：自动判断是否迟到
    // Status Calculation
    public function calculateStatus()
    {
        if (!$this->clock_in) {
            return 'absent';
        }

        // Assuming start_time is a Carbon instance (if it's a datetime field)
        $startTime = $this->schedule->start_time;

        return $this->clock_in <= $startTime ? 'on_time' : 'late';
    }

    // Overtime Calculation
    public function calculateOvertime()
    {
        if (!$this->clock_out) {
            return 0;
        }

        $endTime = Carbon::parse($this->schedule->end_time);

        if (Carbon::parse($this->clock_out)->gt($endTime)) {
            return Carbon::parse($this->clock_out)->diffInMinutes($endTime) / 60; // Convert to hours
        }

        return 0;
    }

}
