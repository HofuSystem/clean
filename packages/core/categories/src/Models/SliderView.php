<?php

namespace Core\Categories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SliderView extends Model
{
  protected $table = 'slider_views';

  protected $fillable = ['slider_id', 'url', 'uuid', 'views_count'];

  /**
   * Boot: auto-generate uuid on creation.
   */
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (empty($model->uuid)) {
        $model->uuid = (string) Str::uuid();
      }
    });
  }

  public function slider()
  {
    return $this->belongsTo(Slider::class, 'slider_id', 'id');
  }
}
