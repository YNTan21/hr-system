<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIRatingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function ratingThresholds()
    {
        return $this->hasMany(RatingThreshold::class);
    }

    public function goals()
    {
        return $this->belongsToMany(Goal::class, 'goal_rating_categories')->withPivot('min_score', 'max_score');
    }
}
