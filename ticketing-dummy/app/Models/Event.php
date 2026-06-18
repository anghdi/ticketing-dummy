<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'image_url',
        'date_time',
        'location',
        'price',
        'quota',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'price' => 'integer',
        'quota' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
