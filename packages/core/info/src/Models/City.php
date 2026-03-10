<?php

namespace Core\Info\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Observers\CityObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Categories\Models\Category;

#[ObservedBy([CityObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class City extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'cities';
	protected $fillable          = ['slug', 'lat', 'lng', 'postal_code', 'image', 'delivery_price', 'status', 'country_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name"];

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
        
        //filter text on  postal_code
        if((request()->has("filters.postal_code")) and !empty(request("filters.postal_code"))){
            $query->where("postal_code","LIKE","%".request("filters.postal_code")."%");
        }
        
        //filter by number on  delivery_price
        if((request()->has("filters.delivery_price")) and !empty(request("filters.delivery_price"))){
            $query->where("delivery_price",request("filters.delivery_price"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }
        
        //filter select on  country
        if((request()->has("filters.country_id")) and !empty(request("filters.country_id"))){
            $query->whereRelation("country","id",request("filters.country_id"));
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
    
    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function districts(){
        return $this->hasMany(District::class, 'city_id', 'id');
    }
    public function categories(){
        return $this->belongsToMany(Category::class,'category_cities');
    }
    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('cities');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('cities');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('cities');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('cities');
    }
    //end Attributes

}
