<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\Country;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Users\Observers\ProfileObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([ProfileObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Profile extends CoreModel {
    
	protected $table             = 'profiles';
	protected $fillable          = ['country_id', 'city_id', 'district_id', 'other_city_name', 'user_id', 'bio', 'lat', 'lng', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  country
        if((request()->has("filters.country_id")) and !empty(request("filters.country_id"))){
            $query->whereRelation("country","id",request("filters.country_id"));
        }
        
        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
        }
        
        //filter select on  district
        if((request()->has("filters.district_id")) and !empty(request("filters.district_id"))){
            $query->whereRelation("district","id",request("filters.district_id"));
        }
        
        //filter text on  other_city_name
        if((request()->has("filters.other_city_name")) and !empty(request("filters.other_city_name"))){
            $query->where("other_city_name","LIKE","%".request("filters.other_city_name")."%");
        }
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
        }
        
        //filter text on  bio
        if((request()->has("filters.bio")) and !empty(request("filters.bio"))){
            $query->where("bio","LIKE","%".request("filters.bio")."%");
        }
        
        //filter by number on  lat
        if((request()->has("filters.lat")) and !empty(request("filters.lat"))){
            $query->where("lat",request("filters.lat"));
        }
        
        //filter text on  lng
        if((request()->has("filters.lng")) and !empty(request("filters.lng"))){
            $query->where("lng","LIKE","%".request("filters.lng")."%");
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

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district(){
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('profiles');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('profiles');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('profiles');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('profiles');
    }
    //end Attributes

}
