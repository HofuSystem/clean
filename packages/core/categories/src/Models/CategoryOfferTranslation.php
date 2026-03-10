<?php

namespace Core\Categories\Models;


use Illuminate\Database\Eloquent\Model;

class CategoryOfferTranslation extends Model
{
   protected $fillable  = ["name","desc"];
   public $timestamps   = false;
}


