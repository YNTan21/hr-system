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
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }
}
