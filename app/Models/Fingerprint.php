<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fingerprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fingerprint_image',
        'fingerprint_image2',
        'fingerprint_image3',
        'fingerprint_image4',
        'fingerprint_image5',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    /**
     * Get the user that owns the fingerprint.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}