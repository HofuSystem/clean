<?php

namespace Core\Users\Models;


use Illuminate\Database\Eloquent\Model;

class RoleTranslation extends Model
{
   protected $fillable  = ["title"];
   public $timestamps   = false;
}


