<?php

namespace Core\Products\Models;


use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
   protected $fillable  = ["name","desc"];
   public $timestamps   = false;
}


