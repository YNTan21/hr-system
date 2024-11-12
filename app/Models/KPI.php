<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPI extends Model
{
    use HasFactory;

    protected $table = 'kpis'; 

    protected $fillable = ['position_id'];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}
