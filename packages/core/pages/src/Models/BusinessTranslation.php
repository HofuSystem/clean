<?php

namespace Core\Pages\Models;


use Illuminate\Database\Eloquent\Model;

class BusinessTranslation extends Model
{
   protected $fillable  = ["title","description"];
   public $timestamps   = false;
}


