<?php

namespace Core\Products\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSettingTranslation extends Model
{
    protected $fillable  = ["name", "description", "locale"];
    public $timestamps   = false;
}
