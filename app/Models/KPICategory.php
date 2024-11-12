<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPICategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}
