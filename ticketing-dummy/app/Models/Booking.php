<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'order_id',
        'snap_token',
        'midtrans_trans_id',
        'event_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'ticket_qty',
        'total_price',
        'status',
    ];

    protected $casts = [
        'ticket_qty' => 'integer',
        'total_price' => 'integer',
        'event_id' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
