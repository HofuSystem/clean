<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\City;
use Core\Categories\Observers\CategoryDateTimeObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CategoryDateTimeObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class CategoryDateTime extends CoreModel {
    
	protected $table             = 'category_date_times';
	protected $fillable          = ['type', 'category_id', 'city_id', 'date', 'from', 'to', 'order_count', 'receiver_count', 'delivery_count', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
        }
        
        //filter select on  category
        if((request()->has("filters.category_id")) and !empty(request("filters.category_id"))){
            $query->whereRelation("category","id",request("filters.category_id"));
        }
        
        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
        }
        
        //filter date on  date
        if((request()->has("filters.from_date")) and !empty(request("filters.from_date"))){
            $query->where("date",">=",Carbon::parse(request("filters.from_date")));
        }

        if((request()->has("filters.to_date")) and !empty(request("filters.to_date"))){
            $query->where("date","<=",Carbon::parse(request("filters.to_date")));
        }
        
        //filter time on  from
        if((request()->has("filters.from_from")) and !empty(request("filters.from_from"))){
            $query->where("from",">=",Carbon::parse(request("filters.from_from")));
        }

        if((request()->has("filters.to_from")) and !empty(request("filters.to_from"))){
            $query->where("from","<=",Carbon::parse(request("filters.to_from")));
        }
        
        //filter time on  to
        if((request()->has("filters.from_to")) and !empty(request("filters.from_to"))){
            $query->where("to",">=",Carbon::parse(request("filters.from_to")));
        }

        if((request()->has("filters.to_to")) and !empty(request("filters.to_to"))){
            $query->where("to","<=",Carbon::parse(request("filters.to_to")));
        }
        
        //filter by number on  order_count
        if((request()->has("filters.order_count")) and !empty(request("filters.order_count"))){
            $query->where("order_count",request("filters.order_count"));
        }
        
        //filter date on  created_at
        if((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))){
            $query->whereDate("created_at",">=",Carbon::parse(request("filters.from_created_at")));
        }

        if((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))){
            $query->whereDate("created_at","<=",Carbon::parse(request("filters.to_created_at")));
        }
        
        //filter date on  updated_at
        if((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))){
            $query->whereDate("updated_at",">=",Carbon::parse(request("filters.from_updated_at")));
        }

        if((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))){
            $query->whereDate("updated_at","<=",Carbon::parse(request("filters.to_updated_at")));
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }
  
    //end Scopes

    //start relations
    
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    //end relations

    //start Attributes


    public function getActionsAttribute(){
        $actions = '<div class ="d-flex justify-content-center">';
      
        if (auth('web')->check() and auth('web')->user()->can('dashboard.category-date-times.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 duplicate-datetime-btn" 
                data-type="' . $this->type . '" 
                data-date="' . $this->date . '" 
                data-category-id="' . $this->category_id . '" 
                data-city-id="' . $this->city_id . '" 
                href="javascript:void(0)"> 
                <i class="fa fa-copy"></i> <span>' . trans('duplicate') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.category-date-times.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.category-date-times.edit', ['type' => $this->type, 'date' => $this->date,'category_id' => $this->category_id,'city_id' => $this->city_id]) . '"> 
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.category-date-times.delete')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.category-date-times.delete', ['type' => $this->type, 'date' => $this->date,'category_id' => $this->category_id,'city_id' => $this->city_id]) . '"> 
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('category-date-times');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('category-date-times');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('category-date-times');
    }
    //end Attributes

}
