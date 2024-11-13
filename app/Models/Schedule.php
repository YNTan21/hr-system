<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'time_in',
        'time_out'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 