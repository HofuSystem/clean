<?php

namespace Core\Pages\Models;


use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
   protected $fillable  = ["title","description","meta_title","meta_description","seo_image","social_share_title","social_share_description","social_share_image"];
   public $timestamps   = false;
}


