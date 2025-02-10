<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePrediction extends Model
{
    protected $fillable = ['date', 'predicted_leaves'];

    protected $casts = [
        'date' => 'date',
        'predicted_leaves' => 'integer'
    ];
} 