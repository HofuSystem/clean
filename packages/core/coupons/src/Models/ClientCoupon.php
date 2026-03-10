<?php

namespace Core\Coupons\Models;

use Illuminate\Database\Eloquent\Model;

class ClientCoupon extends Model
{
    protected $table = "client_coupons";
    protected $fillable = ['client_id','coupon_id'];
}
