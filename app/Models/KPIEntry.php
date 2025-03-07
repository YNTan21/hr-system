<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi_entry';

    protected $fillable = [
        'users_id',
        'goals_id',
        'actual_result',
        'actual_score',
        'final_score',
        'month',
        'year',
        'status',
        'reverted_at',
        'reverted_actual_result',
        'reverted_actual_score'
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    protected $dates = [
        'deleted_at',
        'reverted_at'
    ];

    protected $casts = [
        'reverted_at' => 'datetime',
        'actual_score' => 'integer',
        'actual_result' => 'decimal:2',
        'final_score' => 'decimal:2',
        'reverted_actual_score' => 'decimal:2',
        'reverted_actual_result' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer'
    ];

    public static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            return true; // 跳过任何唯一性检查
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function goal()
    {
        return $this->belongsTo(KPIGoal::class, 'goals_id');
    }
}
