<?php

namespace Core\CMS\Models;


use Illuminate\Database\Eloquent\Model;

class CmsPageDetailTranslation extends Model
{
   protected $fillable  = ["name","description","intro","point"];
   public $timestamps   = false;
}


