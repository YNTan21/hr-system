<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_name',
        'min_start_time',
        'start_time',
        'max_start_time',
        'end_time'
    ];

    // Relationship with Timetable
    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}