<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceDescriptor extends Model
{
    protected $fillable = ['user_id', 'descriptor'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 