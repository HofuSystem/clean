<?php

namespace Core\Users\Models;


use Illuminate\Database\Eloquent\Model;

class PermissionTranslation extends Model
{
   protected $fillable  = ["title"];
   public $timestamps   = false;
}


