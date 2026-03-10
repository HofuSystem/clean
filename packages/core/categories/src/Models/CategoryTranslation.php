<?php

namespace Core\Categories\Models;


use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
   protected $fillable  = ["name","intro","desc","meta_title","meta_description"];
   public $timestamps   = false;
}


