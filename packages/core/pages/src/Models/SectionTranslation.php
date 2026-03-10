<?php

namespace Core\Pages\Models;


use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
   protected $fillable  = ["title","small_title","description"];
   public $timestamps   = false;
}


