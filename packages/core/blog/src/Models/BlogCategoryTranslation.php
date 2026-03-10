<?php

namespace Core\Blog\Models;


use Illuminate\Database\Eloquent\Model;

class BlogCategoryTranslation extends Model
{
   protected $fillable  = ["title"];
   public $timestamps   = false;
}


