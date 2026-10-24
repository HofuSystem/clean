<?php

namespace Core\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLoyaltyAward extends Model
{
    protected $table = 'order_loyalty_awards';

    protected $fillable = [
        'order_id',
        'user_id',
        'eligible_amount',
        'points_earned',
        'points_per_riyal',
    ];

    protected $casts = [
        'eligible_amount' => 'decimal:2',
        'points_earned' => 'integer',
        'points_per_riyal' => 'decimal:4',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
