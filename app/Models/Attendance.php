<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'status'
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
}
