<?php

namespace Core\Coupons\Models;

use Illuminate\Database\Eloquent\Model;

class GiftTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'intro'];
}
