<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'status',
        'overtime'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 添加与 Schedule 的关联
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'user_id', 'user_id')
            ->where('shift_date', $this->date);
    }

    // 添加状态计算方法
    public function calculateStatus()
    {
        // 如果没有找到对应的排班
        if (!$this->schedule) {
            return 'No Schedule';
        }

        // 如果没有打卡
        if (!$this->clock_in_time) {
            return 'Absent';
        }

        $scheduledStart = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->schedule->start_time);
        $clockIn = $this->clock_in_time;

        // 设置迟到阈值（例如：15分钟）
        $lateThreshold = 15;

        // 计算迟到时间（分钟）
        $minutesLate = $clockIn->diffInMinutes($scheduledStart, false);

        if ($minutesLate <= $lateThreshold) {
            return 'On Time';
        } else if ($clockIn->gt($scheduledStart)) {
            return 'Late';
        }

        // 检查早退
        if ($this->clock_out_time) {
            $scheduledEnd = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->schedule->end_time);
            if ($this->clock_out_time->lt($scheduledEnd)) {
                return 'Early Leave';
            }
        }

        return 'Present';
    }
}
