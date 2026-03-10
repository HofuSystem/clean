<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Categories\Observers\CategoryTypeObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CategoryTypeObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class CategoryType extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'category_types';
	protected $fillable          = ['slug', 'category_id', 'hour_price', 'status', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name","intro","desc"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  slug
        if((request()->has("filters.slug")) and !empty(request("filters.slug"))){
            $query->where("slug","LIKE","%".request("filters.slug")."%");
        }
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }
        
        //filter text on  intro
        if((request()->has("filters.intro")) and !empty(request("filters.intro"))){
            $query->where("intro"."_".config("app.locale"),"LIKE","%".request("filters.intro")."%");
        }
        
        //filter text on  desc
        if((request()->has("filters.desc")) and !empty(request("filters.desc"))){
            $query->where("desc"."_".config("app.locale"),"LIKE","%".request("filters.desc")."%");
        }
        
        //filter select on  category
        if((request()->has("filters.category_id")) and !empty(request("filters.category_id"))){
            $query->whereRelation("category","id",request("filters.category_id"));
        }
        
        //filter by number on  hour_price
        if((request()->has("filters.hour_price")) and !empty(request("filters.hour_price"))){
            $query->where("hour_price",request("filters.hour_price"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
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

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('category-types');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('category-types');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('category-types');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('category-types');
    }
    //end Attributes

}
