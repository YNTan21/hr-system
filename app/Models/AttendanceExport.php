<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $attendances;

    public function __construct(array $attendances)
    {
        $this->attendances = $attendances;
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
