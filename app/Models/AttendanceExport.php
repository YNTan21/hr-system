<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $attendances;

    public function __construct($attendances)
    {
        // 转换数据格式
        $this->attendances = $attendances->map(function($attendance) {
            return [
                'Date' => $attendance->date,
                'Employee ID' => $attendance->user->id,
                'Name' => $attendance->user->username,
                'Status' => $attendance->status,
                'Clock In' => $attendance->clock_in_time ? 
                    \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '',
                'Clock Out' => $attendance->clock_out_time ? 
                    \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '',
                'Overtime' => $attendance->overtime
            ];
        })->toArray();
    }

    public function array(): array
    {
        return $this->attendances;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee ID',
            'Name',
            'Status',
            'Clock In',
            'Clock Out',
            'Overtime'
        ];
    }
}
