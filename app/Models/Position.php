<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'position_name',
        'status',
    ];

    // If positions are linked to users, define the relationship
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function kpis()
    {
        return $this->hasMany(KPI::class); 
    }
}