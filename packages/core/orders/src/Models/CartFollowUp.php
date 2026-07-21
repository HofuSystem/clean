<?php

namespace Core\Orders\Models;

use Core\Settings\Models\CoreModel;
use Core\Users\Models\User;
use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalModelObserver::class])]
class CartFollowUp extends CoreModel
{
    protected $table    = 'cart_follow_ups';
    protected $fillable = [
        'cart_id', 'admin_id', 'phone', 'status', 'notes',
        'followed_up_at', 'order_at', 'order_id',
        'creator_id', 'updater_id',
    ];
    protected $casts = [
        'followed_up_at' => 'datetime',
        'order_at'       => 'datetime',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────────

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
