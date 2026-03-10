<?php

namespace Core\Blog\Models;


use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{
   protected $fillable  = ["title","content","meta_title","meta_description"];
   public $timestamps   = false;
}


