<?php

namespace Core\Pages\Models;


use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
   protected $fillable  = ["title","description","meta_title","meta_description"];
   public $timestamps   = false;
}


