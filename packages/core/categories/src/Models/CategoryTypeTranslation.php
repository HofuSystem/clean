<?php

namespace Core\Categories\Models;


use Illuminate\Database\Eloquent\Model;

class CategoryTypeTranslation extends Model
{
   protected $fillable  = ["name","intro","desc"];
   public $timestamps   = false;
}


