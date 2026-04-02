<?php

namespace Core\Categories\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAppFeatureTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['title'];

    protected $table = 'category_app_feature_translations';
}
