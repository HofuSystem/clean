<?php

namespace Core\Pages\Models;


use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
   protected $fillable  = ["question","answer"];
   public $timestamps   = false;
}


