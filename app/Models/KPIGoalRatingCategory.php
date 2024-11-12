<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIGoalRatingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['goal_id', 'rating_category_id', 'min_score', 'max_score'];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function ratingCategory()
    {
        return $this->belongsTo(RatingCategory::class);
    }
}
