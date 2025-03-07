<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_type';

    protected $fillable = [
        'leave_type',
        'code',
        'status',
        'deduct_annual_leave',
        'color'
    ];

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }
}
