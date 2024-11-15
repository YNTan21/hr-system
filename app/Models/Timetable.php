<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $table = 'timetables';  // specify table name if different
    
    protected $fillable = [
        'user_id',
        'date',
        'shift_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
