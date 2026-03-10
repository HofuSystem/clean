<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\City;
use Core\Categories\Observers\SliderObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Categories\Models\SliderView;


#[ObservedBy([SliderObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Slider extends CoreModel
{

    protected $table = 'sliders';
    protected $fillable = ['image_en', 'image_ar', 'category_id', 'link', 'type', 'status', 'city_id', 'creator_id', 'updater_id'];
    protected $guarded = [];


    //start Scopes
    function scopeSearch($query)
    {

        //filter select on  category
        if ((request()->has("filters.category_id")) and !empty(request("filters.category_id"))) {
            $query->whereRelation("category", "id", request("filters.category_id"));
        }

        //filter select on  type
        if ((request()->has("filters.type")) and !empty(request("filters.type"))) {
            $query->where("type", request("filters.type"));
        }

        //filter select on  status
        if ((request()->has("filters.status")) and !empty(request("filters.status"))) {
            $query->where("status", request("filters.status"));
        }

        //filter select on  city
        if ((request()->has("filters.city_id")) and !empty(request("filters.city_id"))) {
            $query->whereRelation("city", "id", request("filters.city_id"));
        }

        //filter date on  created_at
        if ((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))) {
            $query->whereDate("created_at", ">=", Carbon::parse(request("filters.from_created_at")));
        }

        if ((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))) {
            $query->whereDate("created_at", "<=", Carbon::parse(request("filters.to_created_at")));
        }

        //filter date on  updated_at
        if ((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))) {
            $query->whereDate("updated_at", ">=", Carbon::parse(request("filters.from_updated_at")));
        }

        if ((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))) {
            $query->whereDate("updated_at", "<=", Carbon::parse(request("filters.to_updated_at")));
        }
        if (request()->has('trash') and request()->trash == 1) {
            $query->onlyTrashed();
        }
    }

    //end Scopes

    //start relations

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function currentSliderView()
    {
        return $this->hasOne(SliderView::class, 'url', 'link');
    }
    public function sliderView()
    {
        return $this->hasOne(SliderView::class, 'slider_id', 'id');
    }
    public function sliderViews()
    {
        return $this->hasMany(SliderView::class, 'slider_id', 'id');
    }

    //end relations

    //start Attributes

    public function getActionsAttribute()
    {
        return $this->getActions('sliders');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('sliders');
    }
    public function getShowActionsAttribute()
    {
        return $this->getShowActions('sliders');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('sliders');
    }
    //end Attributes

}
