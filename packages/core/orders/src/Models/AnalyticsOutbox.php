<?php

namespace Core\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsOutbox extends Model
{
    protected $table = 'analytics_outbox';

    protected $fillable = [
        'event_key', 'event_name', 'destination', 'order_id', 'payload',
        'status', 'attempts', 'available_at', 'sent_at', 'last_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'available_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
