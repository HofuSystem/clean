<?php

namespace Core\Info\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Observers\DistrictObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([DistrictObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class District extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'districts';
	protected $fillable          = ['slug', 'lat', 'lng', 'postal_code', 'city_id', 'creator_id', 'updater_id'];
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
        
        //filter by number on  lat
        if((request()->has("filters.lat")) and !empty(request("filters.lat"))){
            $query->where("lat",request("filters.lat"));
        }
        
        //filter by number on  lng
        if((request()->has("filters.lng")) and !empty(request("filters.lng"))){
            $query->where("lng",request("filters.lng"));
        }
        
        //filter text on  postal_code
        if((request()->has("filters.postal_code")) and !empty(request("filters.postal_code"))){
            $query->where("postal_code","LIKE","%".request("filters.postal_code")."%");
        }
        
        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
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
    
    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function mapPoints(){
        return $this->hasMany(MapPoint::class, 'district_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('districts');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('districts');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('districts');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('districts');
    }
    //end Attributes

}
