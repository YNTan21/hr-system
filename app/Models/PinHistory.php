<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinHistory extends Model
{
    protected $fillable = ['pin', 'changed_by'];
} 