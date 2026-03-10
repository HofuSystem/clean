<?php

namespace Core\Info\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Observers\CountryObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CountryObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Country extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'countries';
	protected $fillable          = ['slug', 'phonecode', 'short_name', 'flag', 'creator_id', 'updater_id'];
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
        
        //filter text on  phonecode
        if((request()->has("filters.phonecode")) and !empty(request("filters.phonecode"))){
            $query->where("phonecode","LIKE","%".request("filters.phonecode")."%");
        }
        
        //filter text on  short_name
        if((request()->has("filters.short_name")) and !empty(request("filters.short_name"))){
            $query->where("short_name","LIKE","%".request("filters.short_name")."%");
        }
        
        //filter text on  flag
        if((request()->has("filters.flag")) and !empty(request("filters.flag"))){
            $query->where("flag","LIKE","%".request("filters.flag")."%");
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
    
    public function cities(){
        return $this->hasMany(City::class, 'country_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('countries');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('countries');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('countries');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('countries');
    }
    //end Attributes

}
