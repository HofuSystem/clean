<?php

namespace Core\Orders\Models;


use Illuminate\Database\Eloquent\Model;

class ReportReasonTranslation extends Model
{
   protected $fillable  = ["name","desc"];
   public $timestamps   = false;
}


