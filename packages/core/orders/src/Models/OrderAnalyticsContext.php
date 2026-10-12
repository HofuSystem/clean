<?php

namespace Core\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAnalyticsContext extends Model
{
    protected $table = 'order_analytics_contexts';

    protected $fillable = [
        'order_id',
        'schema_version',
        'app_instance_id',
        'session_id',
        'platform',
        'branch_first',
        'branch_latest',
    ];

    protected $casts = [
        'schema_version' => 'integer',
        'session_id' => 'integer',
        'branch_first' => 'array',
        'branch_latest' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
