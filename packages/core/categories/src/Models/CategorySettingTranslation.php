<?php

namespace Core\Categories\Models;


use Illuminate\Database\Eloquent\Model;

class CategorySettingTranslation extends Model
{
    protected $fillable  = ["name", "description"];
   public $timestamps   = false;
}


