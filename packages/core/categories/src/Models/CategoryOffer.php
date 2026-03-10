<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Categories\Observers\CategoryOfferObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CategoryOfferObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class CategoryOffer extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'category_offers';
	protected $fillable          = ['price', 'sale_price', 'image', 'hours_num', 'workers_num', 'status', 'type', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name","desc"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }
        
        //filter text on  desc
        if((request()->has("filters.desc")) and !empty(request("filters.desc"))){
            $query->where("desc"."_".config("app.locale"),"LIKE","%".request("filters.desc")."%");
        }
        
        //filter by number on  price
        if((request()->has("filters.price")) and !empty(request("filters.price"))){
            $query->where("price",request("filters.price"));
        }
        
        //filter by number on  sale_price
        if((request()->has("filters.sale_price")) and !empty(request("filters.sale_price"))){
            $query->where("sale_price",request("filters.sale_price"));
        }
        
        //filter by number on  hours_num
        if((request()->has("filters.hours_num")) and !empty(request("filters.hours_num"))){
            $query->where("hours_num",request("filters.hours_num"));
        }
        
        //filter by number on  workers_num
        if((request()->has("filters.workers_num")) and !empty(request("filters.workers_num"))){
            $query->where("workers_num",request("filters.workers_num"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
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
    
    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('category-offers');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('category-offers');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('category-offers');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('category-offers');
    }
    //end Attributes

}
