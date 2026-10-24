<?php

namespace Core\Orders\Models;

use Core\Settings\Models\CoreModel;
use Core\Users\Models\User;

class OrderAdjustment extends CoreModel
{
    protected $table = 'order_adjustments';

    protected $fillable = [
        'order_id', 'actor_id', 'adjustment_type', 'reason', 'previous_total', 'final_total',
        'value_delta', 'before_snapshot', 'after_snapshot',
    ];

    protected $casts = [
        'previous_total' => 'decimal:2',
        'final_total' => 'decimal:2',
        'value_delta' => 'decimal:2',
        'before_snapshot' => 'array',
        'after_snapshot' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
