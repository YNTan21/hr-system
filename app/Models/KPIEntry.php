<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIEntry extends Model
{
    use HasFactory;

    protected $table = 'kpi_entry';

    protected $fillable = [
        'users_id',
        'goals_id',
        'actual_result',
        'actual_score',
        'final_score',
        'month',
        'year'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goals_id');
    }
}
