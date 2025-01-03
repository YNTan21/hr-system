<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIGoal extends Model
{
    use HasFactory;

    protected $table = 'goals'; 
    
    protected $fillable = [
        'goal_name',
        'goal_score', 
        'goal_type', 
        'position_id',
        'goal_unit',
        'category_score_ranges',
    ];

    // Goal belongs to Position
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kpiEntries()
    {
        return $this->hasMany(KpiEntry::class, 'goals_id');
    }

}
