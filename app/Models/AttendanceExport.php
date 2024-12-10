<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceExport implements FromCollection
{
    public function collection()
    {
        return Attendance::all(); // Adjust this query to fit your data needs
    }
}
