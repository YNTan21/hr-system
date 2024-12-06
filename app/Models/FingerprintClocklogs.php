<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintClocklogs extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'timestamp'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
