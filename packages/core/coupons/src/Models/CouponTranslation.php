<?php

namespace Core\Coupons\Models;


use Illuminate\Database\Eloquent\Model;

class CouponTranslation extends Model
{
   protected $fillable  = ["title", "intro"];

   public $timestamps   = false;
}


