<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'user_id',
        'phone', 
        'address',
        'ic',
        'dob',
        'gender',
        'marital_status',
        'nationality',
        'bank_account_holder_name',
        'bank_name',
        'bank_account_number',
        'hire_date', 
        'position_id', 
        'type',
        'status',
        'annual_leave_balance',
    ];

    protected $casts = [
        'hire_date' => 'date', // Cast hire_date to a date object
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function position():BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function goals()
    {
        return $this->position->goals();
    }

}
